<?php

namespace App\Http\Controllers\supplier;

// Temporary fix - include the model directly
require_once app_path('Models/CashBoxTransaction.php');

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierInvoiceRequest;
use App\Models\CashBox;
use App\Models\CashBoxTransaction;
use App\Models\Category;
use App\Models\CategoryPriceRate;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItems;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class accountStatement extends Controller
{
    public function index($id, Request $request){
        $supplier = Supplier::findOrFail($id);
        $invoices = $supplier->invoices();
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $invoices->where(function($q) use ($search){
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('paid_amount', 'like', "%$search%")
                  ->orWhere('Remaining_amount', 'like', "%$search%")
                  ->orWhere('date', 'like', "%$search%");
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'paid':
                    $invoices->where('states', 'paid');
                    break;
                case 'unpaid':
                    $invoices->where('states', 'unpaid');
                    break;
                case 'partially_paid':
                    $invoices->where('states', 'partially_paid');
                    break;
            }
        }
        
        $invoices = $invoices->latest('date')->get();
        $remaining_amount = $invoices->sum('Remaining_amount');
        return view('supplier.accountStatement.index' ,compact('supplier','invoices' ,'remaining_amount'));
    }

    /**
     * Transaction-based account statement
     */
    public function transactionIndex($id, Request $request){
        $supplier = Supplier::findOrFail($id);
        $transactions = $supplier->transactions()->with(['supplier']);
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $transactions->where(function($q) use ($search){
                $q->where('description', 'like', "%$search%")
                  ->orWhere('amount', 'like', "%$search%")
                  ->orWhere('transaction_date', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%");
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'deposit':
                    $transactions->where('type', 'deposit');
                    break;
                case 'withdrawal':
                    $transactions->where('type', 'withdrawal');
                    break;
            }
        }
        
        $transactions = $transactions->latest('transaction_date')->get();
        
        // Calculate balance
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $balance = $totalDeposits - $totalWithdrawals;
        
        return view('supplier.accountStatement.transaction-index', compact('supplier','transactions','balance','totalDeposits','totalWithdrawals'));
    }
    
    public function createTransaction($id){
        $supplier = Supplier::findOrFail($id);
        return view('supplier.accountStatement.create-transaction', compact('supplier'));
    }

    public function storeTransaction(Request $request, $id){
        $request->validate([
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        $supplier = Supplier::findOrFail($id);
        
        // Create basic transaction with only existing columns
        $transaction = new SupplierTransaction();
        $transaction->supplier_id = $supplier->id;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        
        // Save basic transaction first
        $transaction->save();
        
        // Try to add additional fields if columns exist
        try {
            if (Schema::hasColumn('supplier_transactions', 'description')) {
                $transaction->description = $request->description;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'transaction_date')) {
                $transaction->transaction_date = $request->transaction_date;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'reference_id')) {
                $transaction->reference_id = null;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'reference_type')) {
                $transaction->reference_type = 'manual';
            }
            
            $transaction->save();
        } catch (\Exception $e) {
            // If additional fields fail, at least the basic transaction is saved
            \Log::warning('Could not save additional supplier transaction fields: ' . $e->getMessage());
        }

        Alert::success('نجاح', 'تم إضافة معاملة المورد بنجاح');
        return redirect()->route('supplierAccountStatement.transactionIndex', $supplier->id);
    }
    
    public function create($id){
        $supplier = Supplier::findOrFail($id);
        $products = Product::all();
        return view('supplier.accountStatement.create', compact('supplier', 'products'));
    }
    
    public function show($supplierId, $invoiceId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $invoice = SupplierInvoice::with([
            'items.product',
            'payment'
        ])->findOrFail($invoiceId);
        return view('supplier.accountStatement.show', compact('supplier','invoice'));
    }

    public function store(StoreSupplierInvoiceRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        DB::beginTransaction();
        try {
            // 1. حساب إجمالي الأصناف (دائماً رقم موجب)
            $total = 0;
            foreach ($request->products as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }
            // 2. توليد رقم الفاتورة (INV للشراء و RET للمرتجع)
            $lastInvoice = SupplierInvoice::latest('id')->first();
            $newNum = $lastInvoice ? intval(preg_replace('/[^0-9]/', '', $lastInvoice->invoice_number)) + 1 : 1;
            $prefix = ($request->type == 'purchase') ? 'INV' : 'RET';
            $invoiceNumber = $prefix . str_pad($newNum, 8, '0', STR_PAD_LEFT);

            $paid = $request->paid_amount ?? 0;
            
            // 3. حساب المتبقي (دائماً موجب)
            // المتبقي هنا هو "الفرق" الذي لم يُسوّى بعد في هذه الفاتورة تحديداً
            $remaining = $request->type == 'purchase' ? $total - $paid : 0;
            $states = $request->type == 'purchase' 
                ? ($paid == 0 ? 'unpaid' : ($paid >= $total ? 'paid' : 'partially_paid')) 
                : 'paid'; // المرتجع دائماً يعتبر "مدفوع" لأنه يصب في مصلحتك

            // 4. إنشاء فاتورة المرتجع/الشراء
            $invoice = SupplierInvoice::create([
                'supplier_id'      => $supplier->id,
                'date'             => $request->date,
                'type'             => $request->type, // 'purchase' أو 'return'
                'total_amount'     => $total,
                'paid_amount'      => $paid,
                'Remaining_amount' => max($remaining, 0), // نضمن عدم وجود سالب
                'invoice_number'   => $invoiceNumber,
                'states'           => $states
            ]);

            // 5. معالجة المنتجات والمخزون
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                SupplierInvoiceItems::create([
                    'supplier_invoice_id' => $invoice->id,
                    'product_id'          => $product->id,
                    'quantity'            => $item['quantity'],
                    'unit_price'          => $item['unit_price'],
                ]);

                if ($request->type == 'purchase') {
                    $product->increment('stock', $item['quantity'], ['price_base' => $item['unit_price']]);
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // 6. المنطق المحاسبي للمورد (SupplierTransaction)
            if ($request->type == 'return') {
                
                // تسجيل المرتجع كـ "إيداع" في حسابك (تقليل المديونية)
                SupplierTransaction::create([
                    'supplier_id'      => $supplier->id,
                    'amount'           => $total, 
                    'type'             => 'deposit', // المرتجع دائماً يصب في مصلحتك
                    'description'      => "مرتجع بضاعة فاتورة رقم {$invoice->invoice_number}",
                    'transaction_date' => $request->date,
                    'reference_id'     => $invoice->id,
                    'reference_type'   => 'invoice'
                ]);

                // إذا أخذت كاش من المورد مقابل المرتجع، نسجل حركة سحب من حساب المورد للصندوق
                if ($paid > 0) {
                    $this->createSupplierCashBoxTransaction($paid, $invoice, $supplier, $request);
                }
            } else {
                // حالة الشراء العادية
                SupplierTransaction::create([
                    'supplier_id'      => $supplier->id,
                    'amount'           => $total,
                    'type'             => 'withdrawal', // مديونية عليك
                    'description'      => "فاتورة شراء رقم {$invoice->invoice_number}",
                    'transaction_date' => $request->date,
                    'reference_id'     => $invoice->id,
                    'reference_type'   => 'invoice'
                ]);

                if ($paid > 0) {
                    $this->createSupplierCashBoxTransaction($paid, $invoice, $supplier, $request);
                }
            }

            DB::commit();
            Alert::success('نجاح', 'تم تسجيل العملية وتحديث كشف الحساب');
            return redirect()->route('accountStatement.index', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('خطأ', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create cash box transaction for supplier payment
     */
    private function createSupplierCashBoxTransaction($amount, $invoice, $supplier, $request = null)
    {
        // Get active cash box or create default one
        $cashBox = CashBox::where('status', 'active')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cashBox) {
            // Create default cash box if none exists
            $cashBox = CashBox::create([
                'name' => 'الصندوق الرئيسي',
                'description' => 'صندوق نقدي افتراضي',
                'opening_balance' => 0,
                'current_balance' => 0,
                'total_in' => 0,
                'total_out' => 0,
                'status' => 'active',
                'user_id' => auth()->id()
            ]);
        }

        // Create transaction in cash box
        $description = $invoice 
            ? "دفعة للمورد {$supplier->name} فاتورة {$invoice->invoice_number}"
            : "دفعة للمورد {$supplier->name}";
            
        CashBoxTransaction::create([
            'cash_box_id' => $cashBox->id,
            'type' => 'out',
            'amount' => $request ? $request->paid_amount : $amount,
            'description' => $description,
            'reference_id' => $invoice?->id,
            'reference_type' => 'supplier_invoice',
            'user_id' => auth()->id()
        ]);

        // Update cash box balance
        $cashBox->decrement('current_balance', $amount);
        $cashBox->increment('total_out', $amount);
    }

    public function edit($supplierId, $invoiceId){
        $supplier = Supplier::findOrFail($supplierId);
        $invoice = SupplierInvoice::with('items.product','payment')->findOrFail($invoiceId);
        $products = Product::all(); // كل المنتجات لاختيارها في form
        return view('supplier.accountStatement.edit', compact('supplier','invoice','products'));
    }
    
    /**
     * Show supplier payment records
     */
    public function showPayments($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $payments = SupplierTransaction::with('supplier')
            ->where('supplier_id', $supplierId)
            ->latest('payment_date')
            ->get();
        
        return view('supplier.accountStatement.payments', compact('supplier', 'payments'));
    }
    public function update(StoreSupplierInvoiceRequest $request, $supplierId, $invoiceId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $invoice = SupplierInvoice::with('items','payment')->findOrFail($invoiceId);

        DB::beginTransaction();
        try {
            $invoice->update([
                'date' => $request->date,
                'type' => $request->type,
            ]);
            $existingItemIds = $invoice->items->pluck('id')->toArray();
            $submittedItemIds = collect($request->products)->pluck('id')->filter()->toArray();
            // حذف المنتجات الغير موجودة في التحديث
            $itemsToDelete = array_diff($existingItemIds, $submittedItemIds);
            foreach ($itemsToDelete as $itemId) {
                $item = SupplierInvoiceItems::find($itemId);
                if ($item) {
                    $product = Product::find($item->product_id);
                    // تعديل المخزون حسب نوع الفاتورة
                    if ($invoice->type == 'purchase') {
                        $product->decrement('stock', $item->quantity);
                    } else {
                        $product->increment('stock', $item->quantity);
                    }
                    $item->delete();
                }
            }
            foreach ($request->products as $item) {
                if (isset($item['id']) && in_array($item['id'], $existingItemIds)) {
                    $invoiceItem = SupplierInvoiceItems::findOrFail($item['id']);
                    $product = Product::findOrFail($invoiceItem->product_id);

                    $diff = $item['quantity'] - $invoiceItem->quantity;
                    if ($invoice->type == 'purchase') {
                        $product->increment('stock', $diff);
                    } else {
                        $product->decrement('stock', $diff);
                    }
                    $invoiceItem->update([
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                } else {
                    $product = Product::findOrFail($item['product_id']);
                    SupplierInvoiceItems::create([
                        'supplier_invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                    if ($invoice->type == 'purchase') {
                        $product->increment('stock', $item['quantity']);
                    } else {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }
            $invoice->load('items');
            $total = $invoice->items->sum(fn($item) => $item->quantity * $item->unit_price);
            $paid = $request->paid_amount ?? 0;
            $remaining = $invoice->type == 'purchase'
                ? $total - $paid
                : -($total - $paid);
            $state = $paid == 0 ? 'unpaid' : ($paid == $total ? 'paid' : 'partially_paid');
            $invoice->update([
                'total_amount' => $total,
                'paid_amount' => $paid,
                'Remaining_amount' => $remaining,
                'states' => $state,
            ]);
            if ($paid > 0) {
                if ($invoice->payment) {
                    $invoice->payment->update([
                        'amount' => $paid,
                        'method' => $request->paymentMethod,
                        'payment_date' => now(),
                    ]);
                } else {
                    SupplierTransaction::create([
                        'supplier_id' => $supplier->id,
                        'supplier_invoice_id' => $invoice->id,
                        'amount' => $paid,
                        'method' => $request->paymentMethod,
                        'payment_date' => now(),
                    ]);
                }
            }
            DB::commit();
            Alert::success('نجاح', 'تم تعديل الفاتورة بنجاح');
            return redirect()->route('accountStatement.index', $supplierId);
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('فشل', 'لم يتم تعديل الفاتورة بنجاح');
            return redirect()->route('accountStatement.index', $supplierId)
                        ->with('success', 'تم اضافة الفاتورة بنجاح');
        }
    }

    /**
     * Make payment to supplier from treasury
     */
    public function treasuryPayment(Request $request, $supplierId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);
        $supplier = Supplier::findOrFail($supplierId);
        $cashBox = CashBox::where('status', 'active')
            ->where('user_id', auth()->id())
            ->first();
        if (!$cashBox) {
            Alert::error('فشل', 'لا يوجد صندوق نقدي نشأ. يرجى إنشاء صندوق نقدي أولاً.');
            return redirect()->route('accountStatement.index', $supplierId);
        }
        if($cashBox->current_balance < $request->amount){
            Alert::error('فشل', 'رصيد الصندوق النقدي غير كافي. الرصيد الحالي: ' . number_format($cashBox->current_balance, 2));
            return redirect()->back();
        }
        $totalRemaining = $supplier->invoices()->sum('Remaining_amount');
        if($request->amount > $totalRemaining){
            Alert::error('فشل', 'المبلغ المدفوع أكبر من إجمالي المتبقي على الفواتير. المبلغ المتبقي: ' . number_format($totalRemaining, 2));
            return redirect()->back();
        }

        
        DB::beginTransaction();
        try{
            // Create supplier transaction
            SupplierTransaction::create([
                'supplier_id'=> $supplier->id,
                'amount'=> $request->amount,
                'type' => 'withdrawal',
                'description' => $request->description ?? "دفعة من الخزينة",
                'transaction_date' => now(),
                'reference_id' => $supplier->id,
                'reference_type' => 'manual'
            ]);
            // Create cash box transaction
            CashBoxTransaction::create([
                'cash_box_id' => $cashBox->id,
                'type' => 'out',
                'amount' => $request->amount,
                'description' => "دفعة للمورد {$supplier->name} من الخزينة",
                'reference_id' => $supplier->id,
                'reference_type' => 'manual',
                'user_id' => auth()->id()
            ]);
            // Update cash box balance
            $cashBox->decrement('current_balance', $request->amount);
            $cashBox->increment('total_out', $request->amount);
            // Update supplier invoices
            $this->updateSupplierInvoices($supplier, $request->amount);
            
            
            DB::commit();
            Alert::success('نجاح', 'تم تسجيل الدفعة بنجاح');
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('فشل', 'لم يتم تسجيل الدفعة بنجاح');
        }
    }
    /**
     * Update supplier invoices with payment amount
     */
    private function updateSupplierInvoices($supplier, $paymentAmount)
    {
        $unpaidInvoices = $supplier->invoices()
            ->where('Remaining_amount', '>', 0)
            ->orderBy('date', 'asc')
            ->get();

        $remainingPayment = $paymentAmount;

        foreach ($unpaidInvoices as $invoice) {
            if ($remainingPayment <= 0) break;

            $amountToPay = min($remainingPayment, $invoice->Remaining_amount);
            
            $invoice->update([
                'paid_amount' => $invoice->paid_amount + $amountToPay,
                'Remaining_amount' => $invoice->Remaining_amount - $amountToPay,
                'states' => $invoice->Remaining_amount - $amountToPay <= 0 ? 'paid' : 'partially_paid'
            ]);

            $remainingPayment -= $amountToPay;
        }
    }
    /**
     * Delete supplier invoice
     */
    public function destroy($supplierId, $invoiceId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $invoice = SupplierInvoice::with('items')
            ->where('id', $invoiceId)
            ->where('supplier_id', $supplierId)
            ->firstOrFail();

        DB::transaction(function () use ($supplier, $invoice) {
            // Restore stock for purchase invoices
            if ($invoice->type == 'purchase') {
                foreach ($invoice->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock', $item->quantity);
                    }
                }
            } else {
                // For return invoices, add stock back
                foreach ($invoice->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // Delete invoice items
            SupplierInvoiceItems::where('supplier_invoice_id', $invoice->id)->delete();
            
            // Delete payment records if any
            SupplierTransaction::where('supplier_invoice_id', $invoice->id)->delete();
            
            // Delete the invoice
            $invoice->delete();
        });

        Alert::success('نجاح', 'تم حذف الفاتورة بنجاح');
        return redirect()->route('accountStatement.index', $supplierId);
    }


}
