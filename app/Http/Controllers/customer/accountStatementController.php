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
use App\Services\CustomerWhatsAppStatementService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class accountStatementController extends Controller
{
    public function index($id, Request $request){
        $customer= Customer::findOrFail($id);
        $query = $customer->invoices();

        $this->applyInvoiceListFilters($query, $request);

        $remaining_amount = (clone $query)->sum('remining_amount');
        $invoices = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('customer.accountStatement.index', compact('customer','invoices', 'remaining_amount'));
    }

    /**
     * فتح واتساب مع كشف حساب شهري جاهز للإرسال للعميل.
     */
    public function whatsappMonthlyStatement($id, Request $request, CustomerWhatsAppStatementService $whatsapp)
    {
        $customer = Customer::with('wallet')->findOrFail($id);

        if (blank($customer->phone)) {
            Alert::error('تنبيه', 'لا يوجد رقم هاتف مسجل لهذا العميل. أضف الرقم من تعديل بيانات العميل.');

            return redirect()->route('customerAccountStatement.index', $customer->id);
        }

        $month = $request->input('month', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            Alert::error('تنبيه', 'صيغة الشهر غير صحيحة.');

            return redirect()->route('customerAccountStatement.index', $customer->id);
        }

        return redirect()->away($whatsapp->buildMonthlyStatementUrl($customer, $month));
    }

    /**
     * Search / state / date filters shared by كشف الحساب list and Excel export.
     */
    private function applyInvoiceListFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhere('paid_amount', 'like', "%{$search}%")
                    ->orWhere('remining_amount', 'like', "%{$search}%")
                    ->orWhere('date', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            match ($request->filter) {
                'paid' => $query->where('state', 'paid'),
                'unpaid' => $query->where('state', 'unpaid'),
                'partial' => $query->where('state', 'partial'),
                default => null,
            };
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
    }

    /**
     * Excel export (SpreadsheetML) for one customer's invoices; respects search/filter on كشف الحساب.
     */
    public function exportInvoicesExcel($id, Request $request)
    {
        $customer = Customer::with('wallet')->findOrFail($id);
        $query = $customer->invoices();

        $this->applyInvoiceListFilters($query, $request);

        $invoices = $query->latest('date')->get();
        $xml = $this->buildSingleCustomerInvoicesSpreadsheetXml($customer, $invoices, $request);
        $filename = 'customer-'.$customer->id.'-invoices-'.now()->format('Y-m-d-His').'.xls';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @param  Collection<int, CustomerInvoice>  $invoices
     */
    private function buildSingleCustomerInvoicesSpreadsheetXml(Customer $customer, Collection $invoices, Request $request): string
    {
        $colCount = 7;
        $mergeLast = $colCount - 1;
        $dataRows = $invoices->count();

        $sumTotalAmount = round((float) $invoices->sum('total_amount'), 2);
        $sumPaidAmount = round((float) $invoices->sum('paid_amount'), 2);
        $sumRemainingAmount = round((float) $invoices->sum('remining_amount'), 2);

        $extraTailRows = 2 + ($customer->wallet ? 2 : 0);
        $expandedRows = 4 + $dataRows + $extraTailRows;

        $styles = <<<'XML'
<Styles>
  <Style ss:ID="Default" ss:Name="Normal">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Segoe UI" ss:Size="11"/>
  </Style>
  <Style ss:ID="DocTitle">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="18" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#1F4E79" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0D304E"/>
    </Borders>
  </Style>
  <Style ss:ID="DocMeta">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:Size="11" ss:Color="#2E4057"/>
    <Interior ss:Color="#D6EAF8" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A9CCE3"/>
    </Borders>
  </Style>
  <Style ss:ID="Gap">
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HeadCol">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#2E75B6" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0D304E"/>
    </Borders>
  </Style>
  <Style ss:ID="EvenTxt">
    <Alignment ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#F5FAFD" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="OddTxt">
    <Alignment ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="EvenNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#F5FAFD" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="OddNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="SumLabel">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="11"/>
    <Interior ss:Color="#FFF2CC" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#BF8F00"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
    </Borders>
  </Style>
  <Style ss:ID="SumNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Bold="1" ss:Size="12"/>
    <Interior ss:Color="#FFF2CC" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#BF8F00"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D6B656"/>
    </Borders>
  </Style>
  <Style ss:ID="WalletLabel">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="11" ss:Color="#375623"/>
    <Interior ss:Color="#E2EFDA" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
    </Borders>
  </Style>
  <Style ss:ID="WalletNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Bold="1" ss:Size="12" ss:Color="#375623"/>
    <Interior ss:Color="#E2EFDA" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#548235"/>
    </Borders>
  </Style>
  <Style ss:ID="WalletNote">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Size="11" ss:Italic="1" ss:Color="#375623"/>
    <Interior ss:Color="#F4FFF9" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#C5E0B4"/>
    </Borders>
  </Style>
</Styles>
XML;

        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<?mso-application progid="Excel.Sheet"?>';
        $lines[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $lines[] = $styles;
        $lines[] = '<Worksheet ss:Name="الفواتير">';
        $lines[] = '<Table ss:ExpandedColumnCount="'.$colCount.'" ss:ExpandedRowCount="'.$expandedRows.'" ss:FullColumns="1" ss:FullRows="1" ss:DefaultRowHeight="18">';
        $lines[] = '<Column ss:Width="70"/>';
        $lines[] = '<Column ss:Width="70"/>';
        $lines[] = '<Column ss:Width="60"/>';
        $lines[] = '<Column ss:Width="60"/>';
        $lines[] = '<Column ss:Width="60"/>';
        $lines[] = '<Column ss:Width="60"/>';
        $lines[] = '<Column ss:Width="72"/>';

        $cellOut = static function (string $type, $value): string {
            if ($type === 'Number') {
                return is_numeric($value) ? (string) $value : htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            }
            return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        };

        $emitCell = static function (array $cell, callable $cellOut): string {
            $type = $cell['type'] ?? 'String';
            $value = $cell['value'] ?? '';
            $styleId = $cell['style'] ?? '';
            $mergeAcross = $cell['mergeAcross'] ?? null;
            $attrs = '';
            if ($styleId !== '') {
                $attrs .= ' ss:StyleID="'.htmlspecialchars($styleId, ENT_XML1 | ENT_QUOTES, 'UTF-8').'"';
            }
            if ($mergeAcross !== null) {
                $attrs .= ' ss:MergeAcross="'.(int) $mergeAcross.'"';
            }

            return '<Cell'.$attrs.'><Data ss:Type="'.$type.'">'.$cellOut($type, $value).'</Data></Cell>';
        };

        $row = static function (array $cells) use (&$lines, $cellOut, $emitCell): void {
            $lines[] = '<Row>';
            foreach ($cells as $cell) {
                $lines[] = $emitCell($cell, $cellOut);
            }
            $lines[] = '</Row>';
        };

        $filterNote = '';
        if ($request->filled('search')) {
            $filterNote .= ' — بحث: '.$request->search;
        }
        if ($request->filled('filter')) {
            $filterNote .= ' — تصفية: '.match ($request->filter) {
                'paid' => 'مدفوعة',
                'unpaid' => 'غير مدفوعة',
                'partial' => 'جزئي',
                default => (string) $request->filter,
            };
        }

        $titleLine = 'فواتير العميل: '.$customer->name;

        $row([
            [
                'type' => 'String',
                'value' => $titleLine,
                'style' => 'DocTitle',
                'mergeAcross' => $mergeLast,
            ],
        ]);
        $row([
            [
                'type' => 'String',
                'value' => 'تاريخ التصدير: '.now()->format('Y-m-d H:i').' — عدد الفواتير: '.$dataRows.$filterNote,
                'style' => 'DocMeta',
                'mergeAcross' => $mergeLast,
            ],
        ]);
        $row([
            [
                'type' => 'String',
                'value' => '',
                'style' => 'Gap',
                'mergeAcross' => $mergeLast,
            ],
        ]);

        $row([
            ['type' => 'String', 'value' => 'رقم الفاتورة', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'التاريخ', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'إجمالي الفاتورة', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'المدفوع', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'المتبقي', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'حالة الدفع', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'نوع الفاتورة', 'style' => 'HeadCol'],
        ]);

        $i = 0;
        foreach ($invoices as $invoice) {
            $even = $i % 2 === 0;
            $stT = $even ? 'EvenTxt' : 'OddTxt';
            $stN = $even ? 'EvenNum' : 'OddNum';
            $row([
                ['type' => 'String', 'value' => (string) $invoice->invoice_number, 'style' => $stT],
                ['type' => 'String', 'value' => (string) $invoice->date, 'style' => $stT],
                ['type' => 'Number', 'value' => $invoice->total_amount, 'style' => $stN],
                ['type' => 'Number', 'value' => $invoice->paid_amount, 'style' => $stN],
                ['type' => 'Number', 'value' => $invoice->remining_amount, 'style' => $stN],
                ['type' => 'String', 'value' => $this->invoiceStateLabelExport($invoice->state), 'style' => $stT],
                ['type' => 'String', 'value' => $this->invoiceTypeLabelExport($invoice->type), 'style' => $stT],
            ]);
            $i++;
        }

        $row([
            [
                'type' => 'String',
                'value' => '',
                'style' => 'Gap',
                'mergeAcross' => $mergeLast,
            ],
        ]);

        $row([
            ['type' => 'String', 'value' => 'المجاميع (للفواتير المعروضة أعلاه فقط)', 'style' => 'SumLabel'],
            ['type' => 'String', 'value' => '', 'style' => 'SumLabel'],
            ['type' => 'Number', 'value' => $sumTotalAmount, 'style' => 'SumNum'],
            ['type' => 'Number', 'value' => $sumPaidAmount, 'style' => 'SumNum'],
            ['type' => 'Number', 'value' => $sumRemainingAmount, 'style' => 'SumNum'],
            ['type' => 'String', 'value' => '', 'style' => 'SumLabel'],
            ['type' => 'String', 'value' => '', 'style' => 'SumLabel'],
        ]);

        if ($customer->wallet) {
            $balance = round((float) $customer->wallet->balance, 2);
            $row([
                [
                    'type' => 'String',
                    'value' => 'رصيد المحفظة (أموال متاحة للعميل)',
                    'style' => 'WalletLabel',
                    'mergeAcross' => 4,
                ],
                [
                    'type' => 'Number',
                    'value' => $balance,
                    'style' => 'WalletNum',
                    'mergeAcross' => 1,
                ],
            ]);
            $note = $balance > 0
                ? 'يوجد للعميل رصيد في المحفظة يمكن استخدامه في الدفع التلقائي عند توفر الشروط.'
                : 'لا يوجد حالياً رصيد فعّال في المحفظة (الرصيد صفر).';
            $row([
                [
                    'type' => 'String',
                    'value' => $note,
                    'style' => 'WalletNote',
                    'mergeAcross' => $mergeLast,
                ],
            ]);
        }

        $lines[] = '</Table>';
        $lines[] = '</Worksheet>';
        $lines[] = '</Workbook>';

        return implode("\n", $lines);
    }

    private function invoiceStateLabelExport(?string $state): string
    {
        return match ($state) {
            'paid' => 'مدفوعة بالكامل',
            'partial' => 'مدفوعة جزئياً',
            default => 'غير مدفوعة',
        };
    }

    private function invoiceTypeLabelExport(?string $type): string
    {
        return match ($type) {
            'return' => 'مرتجع',
            default => 'بيع',
        };
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
        $products = Product::where('stock' ,'>=', 0)->get();
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
        $invoice = CustomerInvoice::with(['items.product'])->findOrFail($invoiceId);
        // dd($invoice->items->first()->product);
        return view('customer.accountStatement.show', compact('customer', 'invoice'));
    }
    public function store(StoreCustomerInvoiceRequest $request, $id)
    {
        // dd($request->all());
        $customer = Customer::findOrFail($id);
        // dd($customer->type);
        $invoice = null;
        try{
            DB::transaction(function() use ($request, $customer, &$invoice) {
                // 1. حساب إجمالي الفاتورة
                $total = collect($request->products)->sum(function($item) use ($customer) {
                    $product = Product::with(['category', 'category.priceRate'])->findOrFail($item['product_id']);
                    $unitPrice = $item['unit_price'] ?? $item['price'] ?? 0;
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
                        // create invoice item
                        CustomerInvoiceItems::create([
                            'customer_invoice_id' => $invoice->id,
                            'product_id' => $request->products[0]['product_id'],
                            'quantity' => $request->products[0]['quantity'],
                            'unit_price' => $request->products[0]['unit_price'] ?? $request->products[0]['price'] ?? 0,
                        ]);
                        Alert::success('نجاح', 'تم إرجاع المنتجات بنجاح اسحب الرصيد من الخزنة');
                        return redirect()->route('cashBoxes.show' , $cashBox->id);
                    }
                    if ($totalPaid != $total) {
                        Alert::error('خطأ', 'لا يمكن إنشاء فاتورة غير مدفوعة أو بفائض لعميل عابر. يجب دفع كامل المبلغ.');
                        throw new \Exception('لا يمكن إنشاء فاتورة غير مدفوعة أو بفائض لعميل عابر. يجب دفع كامل المبلغ.');
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
                    
                    $itemPrice = $item['unit_price'] ?? $item['price'] ?? 0;
                    if($itemPrice < $product->price_base){
                        Alert::error('خطأ', "سعر المنتج {$product->name} لا يمكن أن يكون أقل من السعر الأساسي");
                        throw new \Exception("سعر المنتج {$product->name} لا يمكن أن يكون أقل من السعر الأساسي");    
                        
                    }
                    if ($item['quantity'] > $product->stock ){
                        Alert::error('خطأ', "الكمية المطلوبة من المنتج {$product->name} غير متوفرة في المخزون");
                        throw new \Exception("الكمية المطلوبة من المنتج {$product->name} غير متوفرة في المخزون");
                    }
                    $unitPrice = $item['unit_price'] ?? $item['price'] ?? 0;
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
            Alert::success('نجاح', 'تم إنشاء الفاتورة بنجاح!');
            return redirect()->route('customerAccountStatement.index', $customer->id)
                            ->with('success', 'تم إنشاء الفاتورة بنجاح!');
        }catch(\Exception $e){
            Alert::error('خطأ', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
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
