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
        
        $transactions = $transactions->latestTransaction()->get();
        
        // Calculate balance
        $totalSales = $transactions->where('type', 'sale')->sum('amount');
        $totalReturns = $transactions->where('type', 'return')->sum('amount');
        $totalPayments = $transactions->where('type', 'payment')->sum('amount');
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
        DB::transaction(function() use ($request, $customer) {
            $total = collect($request->products)->sum(function($item) use ($customer) {
                // Get product and calculate price based on customer type
                $product = Product::with(['category', 'category.priceRate'])->findOrFail($item['product_id']);
                $unitPrice = $product->getPriceForCustomerType($customer->price_type);
                return $item['quantity'] * $unitPrice;
            });
            
            // Handle wallet payment for permanent customers
            $walletPayment = 0;
            $cashPayment = $request->paid_amount ?? 0;
            
            if ($customer->type === 'permanent' && $request->type === 'payment') {
                $wallet = CustomerWallet::where('customer_id', $customer->id)->first();
                if ($wallet && $wallet->balance > 0) {
                    $walletPayment = min($wallet->balance, $total);
                    $wallet->decrement('balance', $walletPayment);
                }
            }
            
            $totalPaid = $walletPayment + $cashPayment; 
            if ($request->type === 'return') {
                $remaining = 0;
            } else {
                $remaining = $total - $totalPaid;
            } 
            $state = $totalPaid == 0 ? 'unpaid' : ($totalPaid >= $total ? 'paid' : 'partial');
            $prefix = $request->type == 'payment' ? 'INV-' : 'RET-';
            $lastInvoice = CustomerInvoice::where('type', $request->type)
                ->latest('id')
                ->first();
            $lastNum = $lastInvoice ? intval(str_replace($prefix, '', $lastInvoice->invoice_number)) : 0;
            $invoiceNumber = $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
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
            foreach($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                // Calculate unit price based on customer type
                $unitPrice = $product->getPriceForCustomerType($customer->price_type);
                
                CustomerInvoiceItems::create([
                    'customer_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);
                if($request->type === 'payment'){
                    $product->decrement('stock', $item['quantity']);
                } elseif($request->type === 'return') {
                    $product->increment('stock', $item['quantity']);
                }
            }
            
            // For return invoices, add money to customer wallet
            if($request->type === 'return' && $customer->type === 'permanent') {
                $wallet = CustomerWallet::firstOrCreate(
                    ['customer_id' => $customer->id],
                    ['balance' => 0]
                );
                
                // Add the total return amount to customer wallet
                $wallet->increment('balance', $total);
                
                // Also add to cash box (money held for customer)
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
                
                // Add money to cash box
                $cashBox->increment('current_balance', $total);
                $cashBox->increment('total_in', $total);
                // Create cash box transaction record
                CashBoxTransaction::create([
                    'cash_box_id' => $cashBox->id,
                    'type' => 'in',
                    'amount' => $total,
                    'description' => "إرجاع منتجات للعميل {$customer->name} فاتورة {$invoice->invoice_number}",
                    'reference_id' => $invoice->id,
                    'reference_type' => 'customer_return',
                    'user_id' => auth()->id()
                ]);
                // Create customer transaction record
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'amount' => $total,
                    'type' => 'deposit',
                    'transaction_date' => now(),
                    'description' => "إرجاع منتجات فاتورة {$invoice->invoice_number}",
                    'reference_id' => $invoice->id,
                    'reference_type' => 'return_credit'
                ]);
            }
            if($totalPaid > 0) {
                $this->createCashBoxTransaction($cashPayment, $invoice, $customer);
            }
            // Create customer transactions
            if($walletPayment > 0 && $customer->type === 'permanent') {
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'amount' => $walletPayment,
                    'description' => 'دفع من المحفظة',
                    'reference_id' => $invoice->id,
                    'reference_type' => 'invoice',
                ]);
            }
            
            if($cashPayment > 0 && $customer->type === 'permanent') {
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'amount' => $cashPayment,
                    'description' => 'دفع نقدي',
                    'reference_id' => $invoice->id,
                    'reference_type' => 'invoice',
                ]);
            } elseif($cashPayment > 0 && $customer->type === 'walkin') {
                // For walk-in customers, create transaction but no wallet
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => $request->type === 'payment' ? 'payment' : 'return',
                    'amount' => $cashPayment,
                    'description' => $request->type === 'payment' ? 'دفعة فاتورة' : 'إرجاع منتجات',
                    'reference_id' => $invoice->id,
                    'reference_type' => 'invoice',
                ]);
            }
        });
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
        // dd($request->all());
        $customer = Customer::findOrFail($customerId);
        $invoice = CustomerInvoice::with(['items', 'transactions'])->findOrFail($invoiceId);
        DB::transaction(function() use ($request, $customer, $invoice) {
            if ($customer->type === 'permanent') {
                $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id]);
                $oldWalletPayments = $invoice->transactions()->where('description', 'دفع من المحفظة')->sum('amount');
                $wallet->increment('balance', $oldWalletPayments);
            }
            $invoice->transactions()->delete();
            $existingItemIds = $invoice->items->pluck('id')->toArray();
            $submittedItems = collect($request->products);
            $submittedItemIds = $submittedItems->pluck('id')->filter()->toArray();
            // أ. حذف المنتجات التي أزالها المستخدم من الواجهة
            $itemsToDelete = array_diff($existingItemIds, $submittedItemIds);
            foreach ($itemsToDelete as $itemId) {
                $item = CustomerInvoiceItems::find($itemId);
                $product = Product::find($item->product_id);
                // عكس المخزون
                if ($invoice->type === 'payment') { $product->increment('stock', $item->quantity); }
                else { $product->decrement('stock', $item->quantity); }
                $item->delete();
            }

            // ب. تحديث الموجود أو إضافة الجديد
            foreach ($request->products as $itemData) {
                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    // تحديث صنف موجود
                    $invoiceItem = CustomerInvoiceItems::find($itemData['id']);
                    $product = Product::find($invoiceItem->product_id);
                    
                    // حساب فرق الكمية لتعديل المخزون
                    $diff = $itemData['quantity'] - $invoiceItem->quantity;
                    if ($invoice->type === 'payment') { $product->decrement('stock', $diff); }
                    else { $product->increment('stock', $diff); }

                    $invoiceItem->update([
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $product->getPriceForCustomerType($customer->price_type),
                    ]);
                } else {
                    // إضافة صنف جديد تماماً للفاتورة
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
            $total = $invoice->items->sum(fn($i) => $i->quantity * $i->unit_price);
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

            // 5. التحديث النهائي للفاتورة
            $invoice->update([
                'date' => $request->date,
                'type' => $request->type,
                'total_amount' => $total,
                'paid_amount' => $totalPaid,
                'remining_amount' => $remaining,
                'state' => $state,
            ]);

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
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'payment_type' => 'required|in:wallet,invoice'
        ]);
        
        $customer = Customer::findOrFail($customerId);
        
        // For wallet deposits, create cash box if it doesn't exist
        // For treasury payments, validate existing cash box
        if ($request->payment_type === 'wallet') {
            $cashBox = CashBox::where('status', 'active')
                ->where('user_id', auth()->id())
                ->first();

            if (!$cashBox) {
                // Create default cash box for wallet deposits
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
        } else {
            // Check if there's an active cash box before processing payment
            $cashBox = CashBox::where('status', 'active')
                ->where('user_id', auth()->id())
                ->first();

            if (!$cashBox) {
                Alert::error('فشل', 'لا يوجد صندوق نقدي نشأ. يرجى إنشاء صندوق نقدي أولاً.');
                return redirect()->back();
            }

            // Check if cash box has sufficient balance
            if ($cashBox->current_balance < $request->amount) {
                Alert::error('فشل', 'رصيد الصندوق النقدي غير كافي. الرصيد الحالي: ' . number_format($cashBox->current_balance, 2));
                return redirect()->back();
            }
        }
        
        DB::transaction(function() use ($request, $customer, $cashBox) {
            if ($request->payment_type === 'wallet') {
                // Add to customer wallet/balance and add to cash box
                $wallet = CustomerWallet::firstOrCreate(
                    ['customer_id' => $customer->id],
                    ['balance' => 0]
                );
                $wallet->increment('balance', $request->amount);
                
                // Add money to cash box (this is a liability/money held for customer)
                if ($cashBox) {
                    $cashBox->increment('current_balance', $request->amount);
                    $cashBox->increment('total_in', $request->amount);
                    
                    // Create cash box transaction record
                    CashBoxTransaction::create([
                        'cash_box_id' => $cashBox->id,
                        'type' => 'in',
                        'amount' => $request->amount,
                        'description' => "إيداع في رصيد العميل {$customer->name}",
                        'reference_id' => $customer->id,
                        'reference_type' => 'customer_wallet_deposit',
                        'user_id' => auth()->id()
                    ]);
                }
                
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'amount' => $request->amount,
                    'type' => 'deposit',
                    'transaction_date' => now(),
                    'description' => $request->description ?? 'إيداع في رصيد العميل',
                    'reference_type' => 'wallet_deposit'
                ]);
                
                // Check if customer has unpaid invoices and offer auto-payment
                $unpaidInvoices = $customer->invoices()
                    ->where('remining_amount', '>', 0)
                    ->orderBy('date', 'asc')
                    ->get();
                
                if ($unpaidInvoices->isNotEmpty()) {
                    $totalUnpaid = $unpaidInvoices->sum('remining_amount');
                    
                    // If customer has enough balance, auto-pay invoices
                    if ($wallet->balance >= $totalUnpaid) {
                        $this->updateCustomerInvoices($customer, $totalUnpaid);
                        $wallet->decrement('balance', $totalUnpaid);
                        
                        // Remove money from cash box when auto-paying invoices
                        if ($cashBox) {
                            $cashBox->decrement('current_balance', $totalUnpaid);
                            $cashBox->increment('total_out', $totalUnpaid);
                            
                            CashBoxTransaction::create([
                                'cash_box_id' => $cashBox->id,
                                'type' => 'out',
                                'amount' => $totalUnpaid,
                                'description' => "سداد فواتير العميل {$customer->name}",
                                'reference_id' => $customer->id,
                                'reference_type' => 'auto_invoice_payment',
                                'user_id' => auth()->id()
                            ]);
                        }
                        
                        CustomerTransaction::create([
                            'customer_id' => $customer->id,
                            'amount' => $totalUnpaid,
                            'type' => 'withdrawal',
                            'transaction_date' => now(),
                            'description' => 'سداد تلقائي للفواتير من رصيد العميل',
                            'reference_type' => 'auto_invoice_payment'
                        ]);
                        
                        $message = 'تم إيداع المبلغ في رصيد العميل وسداد جميع الفواتير المستحقة تلقائياً';
                    } else {
                        $message = 'تم إيداع المبلغ في رصيد العميل. يوجد فواتير مستحقة غير مدفوعة بقيمة ' . number_format($totalUnpaid, 2);
                    }
                } else {
                    $message = 'تم إيداع المبلغ في رصيد العميل بنجاح';
                }
                
            } else {
                // Pay invoices from treasury (deduct from cash box)
                $this->createCustomerCashBoxTransaction($request->amount, null, $customer, $request);
                
                CustomerTransaction::create([
                    'customer_id' => $customer->id,
                    'amount' => $request->amount,
                    'type' => 'withdrawal',
                    'transaction_date' => now(),
                    'description' => $request->description ?? 'دفعة من الخزينة',
                    'reference_type' => 'treasury_payment'
                ]);
                
                // Update customer invoices (pay from oldest)
                $this->updateCustomerInvoices($customer, $request->amount);
                
                $message = 'تم دفع الفواتير بنجاح';
            }
        });

        return response()->json([
            'success' => true,
            'message' => $message ?? 'تمت العملية بنجاح'
        ]);
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
