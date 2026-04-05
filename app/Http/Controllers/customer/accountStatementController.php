<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerInvoiceRequest;
use App\Models\CashBox;
use App\Models\CashBoxTransaction;
use App\Models\Category;
use App\Models\CategoryPriceRate;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceItems;
use App\Models\CustomerTransaction;
use App\Models\CustomerWallet;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class accountStatementController extends Controller
{
    public function index($id, Request $request){
        $customer= Customer::findOrFail($id);
        $invoices = $customer->invoices();
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $invoices->where(function($q) use ($search){
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('paid_amount', 'like', "%$search%")
                  ->orWhere('remining_amount', 'like', "%$search%")
                  ->orWhere('date', 'like', "%$search%");
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'paid':
                    $invoices->where('state', 'paid');
                    break;
                case 'unpaid':
                    $invoices->where('state', 'unpaid');
                    break;
                case 'partial':
                    $invoices->where('state', 'partial');
                    break;
            }
        }
        
        $invoices = $invoices->latest('date')->get();
        $remaining_amount = $invoices->sum('remining_amount');
        return view('customer.accountStatement.index', compact('customer','invoices', 'remaining_amount'));
    }

    /**
     * Transaction-based account statement
     */
    public function transactionIndex($id, Request $request){
        $customer = Customer::findOrFail($id);
        $transactions = $customer->transactions()->with(['customer']);
        
        // Add date range search functionality
        if($request->filled('search_from_date') && $request->filled('search_to_date')){
            $transactions->whereDate('created_at', '>=', $request->search_from_date)
                         ->whereDate('created_at', '<=', $request->search_to_date);
        } elseif($request->filled('search_from_date')){
            $transactions->whereDate('created_at', '>=', $request->search_from_date);
        } elseif($request->filled('search_to_date')){
            $transactions->whereDate('created_at', '<=', $request->search_to_date);
        }
        
        // Add regular search functionality
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
                case 'payment':
                    $transactions->where('type', 'payment');
                    break;
                case 'sale':
                    $transactions->where('type', 'sale');
                    break;
                case 'return':
                    $transactions->where('type', 'return');
                    break;
            }
        }
        
        // Add pagination
        $transactions = $transactions->latestTransaction()->paginate(10);
        
        // Preserve query parameters in pagination
        $transactions->appends($request->query());
        
        // Calculate balance from all transactions (not just paginated)
        $allTransactions = $customer->transactions()->get();
        $totalSales = $allTransactions->where('type', 'sale')->sum('amount');
        $totalReturns = $allTransactions->where('type', 'return')->sum('amount');
        $totalPayments = $allTransactions->where('type', 'payment')->sum('amount');
        $balance = ($totalSales - $totalReturns) - $totalPayments;
        
        return view('customer.accountStatement.transaction-index', compact('customer','transactions','balance','totalSales','totalReturns','totalPayments'));
    }
    
    public function create($id){
        $customer = Customer::findOrFail($id);
        $products = Product::where('stock' ,'>', 0)->get();
        return view('customer.accountStatement.create', compact('customer', 'products'));
    }

    public function createTransaction($id){
        $customer = Customer::findOrFail($id);
        return view('customer.accountStatement.create-transaction', compact('customer'));
    }

    public function storeTransaction(Request $request, $id){
        $request->validate([
            'type' => 'required|in:sale,return,payment,adjustment',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'method' => 'nullable|in:cash,wallet,bank',
            'transaction_date' => 'required|date'
        ]);

        $customer = Customer::findOrFail($id);
        
        // Create basic transaction with only existing columns
        $transaction = new CustomerTransaction();
        $transaction->customer_id = $customer->id;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        
        // Save basic transaction first
        $transaction->save();
        Alert::success('نجاح', 'تم إضافة المعاملة بنجاح');
        return redirect()->route('customerAccountStatement.transactionIndex', $customer->id);
    }
    public function show($customerId, $invoiceId){
        $customer = Customer::findOrFail($customerId);
        $invoice = CustomerInvoice::with('items')->findOrFail($invoiceId);
        return view('customer.accountStatement.show', compact('customer', 'invoice'));
    }
    public function store(StoreCustomerInvoiceRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $invoice = null;
        DB::transaction(function() use ($request, $customer, &$invoice) {
            // 1. حساب إجمالي الفاتورة
            // dd($request->products);
            $total = collect($request->products)->sum(function($item) use ($customer) {
                $product = Product::with(['category', 'category.priceRate'])->findOrFail($item['product_id']);
                $unitPrice = $item['price'];
                return $item['quantity'] * $unitPrice;
            });

            $cashPayment = $request->paid_amount ?? 0;
            $walletPayment = 0;

            // 2. إدارة المحفظة للعميل الدائم
            if ($customer->type === 'permanent' && $request->type === 'payment') {
                $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);
                if ($wallet->balance > 0) {
                    $walletPayment = min($wallet->balance, $total);
                    $wallet->decrement('balance', $walletPayment);
                }
            }
            $totalPaid = $cashPayment + $walletPayment;
            // 3. التحقق حسب نوع العميل
            if ($customer->type === 'walkin') {
                if($request->type === 'return' ){
                    $product = Product::findOrFail($request->products[0]['product_id']);
                    $cashBox = CashBox::where('status', 'active')
                        ->where('user_id', auth()->id())
                        ->first();
                    $product->increment('stock', $request->products[0]['quantity']);
                    $invoice = CustomerInvoice::create([
                        'invoice_number' => 'RET-' . str_pad(CustomerInvoice::where('type', 'return')->count() + 1, 5, '0', STR_PAD_LEFT),
                        'customer_id' => $customer->id,
                        'date' => $request->date,
                        'total_amount' => $total,
                        'paid_amount' => 0,
                        'remining_amount' => 0,
                        'state' => 'paid',
                        'type' => 'return',
                    ]);
                    Alert::success('نجاح', 'تم إرجاع المنتجات بنجاح اسحب الرصيد من الخزنة');
                    return redirect()->route('cashBoxes.show' , $cashBox->id);
                }
                if ($totalPaid != $total) {
                    Alert::error('خطأ', 'لا يمكن إنشاء فاتورة غير مدفوعة أو بفائض لعميل عابر. يجب دفع كامل المبلغ.');
                    return redirect()->back()->withInput();
                }
                $remaining = 0;
                
            } else {
                // العميل الدائم
                if ($totalPaid > $total) {
                    $excessAmount = $totalPaid - $total;
                    $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id]);
                    $wallet->increment('balance', $excessAmount);
                    $totalPaid = $total; // ضبط الفاتورة
                }
                $remaining = $total - $totalPaid;
            }

            $state = $totalPaid == 0 ? 'unpaid' : ($totalPaid >= $total ? 'paid' : 'partial');

            // 4. رقم الفاتورة
            $prefix = $request->type === 'payment' ? 'INV-' : 'RET-';
            $lastInvoice = CustomerInvoice::where('type', $request->type)->latest('id')->first();
            $lastNum = $lastInvoice ? intval(str_replace($prefix, '', $lastInvoice->invoice_number)) : 0;
            $invoiceNumber = $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);

            // 5. إنشاء الفاتورة
            $invoice = CustomerInvoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customer->id,
                'date' => $request->date,
                'total_amount' => $total,
                'paid_amount' => $totalPaid,
                'remining_amount' => $remaining,
                'state' => $state,
                'type' => $request->type,
            ]);
            
            // 6. تسجيل المنتجات وتحديث المخزون
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $item['price'];
                CustomerInvoiceItems::create([
                    'customer_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);

                if ($request->type === 'payment') {
                    $product->decrement('stock', $item['quantity']);
                } elseif ($request->type === 'return') {
                    $product->increment('stock', $item['quantity']);
                }
            }
            // 7. التعامل مع الخزنة
            if ($cashPayment > 0) {
                $cashBox = CashBox::where('status', 'active')
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$cashBox) {
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

                $cashBox->increment('current_balance', $cashPayment);
                $cashBox->increment('total_in', $cashPayment);
            }

            // 8. تسجيل المعاملات النقدية والمحفظة
            if ($walletPayment > 0 && $customer->type === 'permanent') {
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'amount' => $walletPayment,
                    'description' => 'دفع من المحفظة',
                    'reference_id' => $invoice->id,
                    'reference_type' => 'invoice',
                ]);
            }

            if ($cashPayment > 0) {
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'amount' => $cashPayment,
                    'description' => $request->type === 'payment' ? 'دفعة نقدية' : 'إرجاع نقدي',
                    'reference_id' => $invoice->id,
                    'reference_type' => 'invoice',
                ]);
            }

            // 9. إدارة المرتجعات للعميل الدائم
            if ($request->type === 'return' && $customer->type === 'permanent') {
                $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);
                $wallet->increment('balance', $total);

                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'amount' => $total,
                    'type' => 'return',
                    'transaction_date' => now(),
                    'description' => "إرجاع منتجات فاتورة {$invoice->invoice_number}",
                    'reference_id' => $invoice->id,
                    'reference_type' => 'return_credit'
                ]);
                $invoice->update([
                    'paid_amount' => 0,
                    'remining_amount' => 0,
                    'state' => 'paid'
                ]);
                Alert::success('نجاح', 'تم إرجاع المنتجات وإضافة الرصيد إلى المحفظة بنجاح');
            }

        });
        // create cash box transaction for cash payment
        if ($request->paid_amount > 0 && $invoice) {
            $this->createCashBoxTransaction($request->paid_amount, $invoice, $customer);
        }

        return redirect()->route('customerAccountStatement.index', $customer->id)
                        ->with('success', 'تم إنشاء الفاتورة بنجاح!');
    }
    /**
     * Create cash box transaction for customer payment
     */
    private function createCashBoxTransaction($cashAmount, $invoice, $customer)
    {
        if ($cashAmount <= 0) {
            return; // No cash payment to record
        }
        
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
        CashBoxTransaction::create([
            'cash_box_id' => $cashBox->id,
            'type' => 'in',
            'amount' => $cashAmount,
            'description' => "دفعة فاتورة {$invoice->invoice_number} من العميل {$customer->name}",
            'reference_id' => $invoice->id,
            'reference_type' => 'customer_invoice',
            'user_id' => auth()->id()
        ]);

        // Update cash box balance
        $cashBox->increment('current_balance', $cashAmount);
        $cashBox->increment('total_in', $cashAmount);
    }

    public function edit($customerId, $invoiceId){
        $customer = Customer::findOrFail($customerId);
        $invoice = CustomerInvoice::with('items')->findOrFail($invoiceId);
        $products = Product::where('stock', '>', 0)->get();
        return view('customer.accountStatement.edit', compact('customer', 'invoice', 'products'));
    }
    public function update(StoreCustomerInvoiceRequest $request, $customerId, $invoiceId)
    {
        $customer = Customer::findOrFail($customerId);
        $invoice = CustomerInvoice::with(['items', 'transactions'])->findOrFail($invoiceId);
        DB::transaction(function() use ($request, $customer, $invoice) {
            // total of the invoice 
            $total = collect($request->products)->sum(function($item) use ($customer) {
                $product = Product::with(['category', 'category.priceRate'])->findOrFail($item['product_id']);
                $unitPrice = $product->getPriceForCustomerType($customer->price_type);
                return $item['quantity'] * $unitPrice;
            });
            // dd($total , $request->paid_amount);
            
            if ($customer->type === 'permanent') {
                $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id]);
                $oldWalletPayments = $invoice->transactions()->where('description', 'دفع من المحفظة')->sum('amount');
                $wallet->increment('balance', $oldWalletPayments);
            }
            $invoice->transactions()->delete();
            $existingItemIds = $invoice->items->pluck('id')->toArray();
            $submittedItems = collect($request->products);
            $submittedItemIds = $submittedItems->pluck('id')->filter()->toArray();
            $itemsToDelete = array_diff($existingItemIds, $submittedItemIds);
            foreach ($itemsToDelete as $itemId) {
                $item = CustomerInvoiceItems::find($itemId);
                $product = Product::find($item->product_id);
                if ($invoice->type === 'payment') { $product->increment('stock', $item->quantity); }
                else { $product->decrement('stock', $item->quantity); }
                $item->delete();
            }
            foreach ($request->products as $itemData) {
                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    $invoiceItem = CustomerInvoiceItems::find($itemData['id']);
                    $product = Product::find($invoiceItem->product_id);
                    $diff = $itemData['quantity'] - $invoiceItem->quantity;
                    if ($invoice->type === 'payment') { $product->decrement('stock', $diff); }
                    else { $product->increment('stock', $diff); }
                    $invoiceItem->update([
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $product->getPriceForCustomerType($customer->price_type),
                    ]);
                } else {
                    $newProduct = Product::findOrFail($itemData['product_id']);
                    CustomerInvoiceItems::create([
                        'customer_invoice_id' => $invoice->id,
                        'product_id' => $newProduct->id,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $newProduct->getPriceForCustomerType($customer->price_type),
                    ]);

                    if ($invoice->type === 'payment') { $newProduct->decrement('stock', $itemData['quantity']); }
                    else { $newProduct->increment('stock', $itemData['quantity']); }
                }
            }
            // 3. إعادة حساب الإجماليات بعد استقرار المنتجات
            $invoice->refresh(); // تحديث العلاقة items
            $cashPayment = $request->paid_amount ?? 0;
            $walletPayment = 0;

            // 4. معالجة الدفع والمحفظة بناءً على الإجمالي الجديد
            if ($customer->type === 'permanent' && $request->type === 'payment') {
                $wallet = CustomerWallet::where('customer_id', $customer->id)->first();
                $neededFromWallet = max(0, $total - $cashPayment);
                $walletPayment = min($wallet->balance, $neededFromWallet);
                
                if ($walletPayment > 0) {
                    $wallet->decrement('balance', $walletPayment);
                    CustomerTransaction::create([
                        'customer_id' => $customer->id,
                        'customer_invoice_id' => $invoice->id,
                        'type' => 'payment',
                        'amount' => $walletPayment,
                        'description' => 'دفع من المحفظة',
                        'reference_type' => 'invoice',
                    ]);
                }
            }

            $totalPaid = $cashPayment + $walletPayment;
            $remaining = ($request->type === 'payment') ? ($total - $totalPaid) : -($total - $totalPaid);
            $state = $totalPaid <= 0 ? 'unpaid' : ($totalPaid >= $total ? 'paid' : 'partial');
            if($request->paid_amount > $total && $customer->type === 'permanent'){
                $excessAmount = $request->paid_amount - $total;
                $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);
                $wallet->increment('balance', $excessAmount);
                $invoice->update([
                    'date' => $request->date,
                    'type' => $request->type,
                    'total_amount' => $total,
                    'paid_amount' => $total ,
                    'remining_amount' => 0,
                    'state' => 'paid',
                ]);
            }else{
                $invoice->update([
                'date' => $request->date,
                'type' => $request->type,
                'total_amount' => $total,
                'paid_amount' => $totalPaid,
                'remining_amount' => $remaining,
                'state' => $state,
            ]);
            }

            // 5. التحديث النهائي للفاتورة
            

            if ($cashPayment > 0) {
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'customer_invoice_id' => $invoice->id,
                    'type' => 'payment',
                    'amount' => $cashPayment,
                    'description' => 'دفعة نقدية',
                    'reference_type' => 'invoice',
                ]);
            }
        });

        return redirect()->route('customerAccountStatement.index', $customer->id)
                        ->with('success', 'تم تحديث الفاتورة والمخزون بنجاح!');
    }
    public function destroy($customerId, $invoiceId)
    {
        $customer = Customer::findOrFail($customerId);

        $invoice = CustomerInvoice::with('items')
            ->where('id', $invoiceId)
            ->where('customer_id', $customerId) // حماية
            ->firstOrFail();

        DB::transaction(function () use ($customer, $invoice) {
            if ($invoice->type == 'payment') {
                foreach ($invoice->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }
            CustomerInvoiceItems::where('customer_invoice_id', $invoice->id)->delete();
            if ($invoice->paid_amount > 0 && $customer->type === 'permanent') {
                $wallet = CustomerWallet::where('customer_id', $customer->id)->first();
                if ($wallet) {
                    // خصم المبلغ المدفوع فقط من رصيد العميل
                    $wallet->balance -= $invoice->paid_amount;
                    $wallet->save();
                }
            }
            $invoice->delete();
        });

        Alert::success('نجاح', 'تم حذف الفاتورة بنجاح');
        return redirect()->route('customerAccountStatement.index', $customer->id);
    }

    /**
     * Make payment to customer from treasury
     */
    public function treasuryPayment(Request $request, $customerId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);
        $customer = Customer::findOrFail($customerId);
        $wallet = $customer->wallet;
        $cashBox = CashBox::where('status', 'active')->where('user_id', auth()->id())->first();
        if(!$wallet || $wallet->balance < $request->amount){
            Alert::error('خطأ', 'لا يمكنك السحب الرصيد في المحفظة غير كافي لإجراء هذه العملية');
            return redirect()->back();
        }
        if(!$cashBox || $cashBox->current_balance < $request->amount){
            Alert::error('خطأ', 'لا يمكنك السحب الرصيد في الصندوق غير كافي لإجراء هذه العملية');
            return redirect()->back();
        }
        DB::beginTransaction();
        try{
            $wallet->decrement('balance', $request->amount);
            CustomerTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'payment',
                'amount' => $request->amount,
                'description' => $request->description ?? 'سحب من المحفظة',
                'reference_type' => 'treasury_payment',
                'transaction_date' => now(),

            ]);
            CashBoxTransaction::create([
                'cash_box_id' => $cashBox->id,
                'type' => 'out',
                'amount' => $request->amount,
                'description' => "سحب لصالح العميل {$customer->name}",
                'reference_type' => 'treasury_payment',
                'user_id' => auth()->id(),
            ]);
            $cashBox->decrement('current_balance', $request->amount);
            $cashBox->increment('total_out', $request->amount);
            DB::commit();
            Alert::success('نجاح', 'تم السحب بنجاح');
            return redirect()->back();
        }
        catch(\Exception $e){
            DB::rollBack();
            Alert::error('خطأ', 'حدث خطأ أثناء السحب: ' . $e->getMessage());
            return redirect()->back();
        }           
    }

    /**
     * Create cash box transaction for customer payment
     */
    private function createCustomerCashBoxTransaction($amount, $invoice, $customer, $request = null)
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
            ? "دفعة للعميل {$customer->name} فاتورة {$invoice->invoice_number}"
            : "دفعة للعميل {$customer->name}";
            
        CashBoxTransaction::create([
            'cash_box_id' => $cashBox->id,
            'type' => 'out',
            'amount' => $request ? $request->amount : $amount,
            'description' => $description,
            'reference_id' => $invoice?->id,
            'reference_type' => 'customer_invoice',
            'user_id' => auth()->id()
        ]);

        // Update cash box balance
        $cashBox->decrement('current_balance', $amount);
        $cashBox->increment('total_out', $amount);
    }

    /**
     * Update customer invoices with payment amount
     */
    private function updateCustomerInvoices($customer, $paymentAmount)
    {
        $unpaidInvoices = $customer->invoices()
            ->where('remining_amount', '>', 0)
            ->orderBy('date', 'asc')
            ->get();

        $remainingPayment = $paymentAmount;

        foreach ($unpaidInvoices as $invoice) {
            if ($remainingPayment <= 0) break;

            $amountToPay = min($remainingPayment, $invoice->remining_amount);
            
            $invoice->update([
                'paid_amount' => $invoice->paid_amount + $amountToPay,
                'remining_amount' => $invoice->remining_amount - $amountToPay,
                'state' => $invoice->remining_amount - $amountToPay <= 0 ? 'paid' : 'partial'
            ]);

            $remainingPayment -= $amountToPay;
        }
    }

}
