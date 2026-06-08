<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceItems;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $dailyStart = Carbon::today()->subDays(29);
        $monthlyStart = Carbon::now()->startOfMonth()->subMonths(11);

        $stats = [
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),
            'products' => Product::count(),
            'monthInvoices' => CustomerInvoice::where('type', 'payment')
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count(),
        ];

        $kpis = [
            'todaySales' => $this->sumCustomerInvoices('payment', $today, $today),
            'monthSales' => $this->sumCustomerInvoices('payment', $monthStart, $monthEnd),
            'todayReturns' => $this->sumCustomerInvoices('return', $today, $today),
            'monthReturns' => $this->sumCustomerInvoices('return', $monthStart, $monthEnd),
            'todayPurchases' => $this->sumSupplierInvoices('purchase', $today, $today),
            'monthPurchases' => $this->sumSupplierInvoices('purchase', $monthStart, $monthEnd),
            'todayCollected' => $this->sumCustomerPaid('payment', $today, $today),
            'monthCollected' => $this->sumCustomerPaid('payment', $monthStart, $monthEnd),
            'outstanding' => (float) CustomerInvoice::where('type', 'payment')->sum('remining_amount'),
            'netMonthSales' => $this->sumCustomerInvoices('payment', $monthStart, $monthEnd)
                - $this->sumCustomerInvoices('return', $monthStart, $monthEnd),
        ];

        $chartData = [
            'daily' => [
                'labels' => $this->dailyLabels($dailyStart, $today),
                'sales' => $this->dailyCustomerSeries($dailyStart, $today, 'payment', 'total_amount'),
                'returns' => $this->dailyCustomerSeries($dailyStart, $today, 'return', 'total_amount'),
                'purchases' => $this->dailySupplierSeries($dailyStart, $today, 'purchase'),
                'collected' => $this->dailyCustomerSeries($dailyStart, $today, 'payment', 'paid_amount'),
            ],
            'monthly' => [
                'labels' => $this->monthlyLabels($monthlyStart, Carbon::now()),
                'sales' => $this->monthlyCustomerSeries($monthlyStart, Carbon::now(), 'payment', 'total_amount'),
                'returns' => $this->monthlyCustomerSeries($monthlyStart, Carbon::now(), 'return', 'total_amount'),
                'purchases' => $this->monthlySupplierSeries($monthlyStart, Carbon::now(), 'purchase'),
                'collected' => $this->monthlyCustomerSeries($monthlyStart, Carbon::now(), 'payment', 'paid_amount'),
            ],
            'paymentStatus' => $this->paymentStatusBreakdown($monthStart, $monthEnd),
            'topProducts' => $this->topProducts($monthStart, $monthEnd),
        ];

        return view('dashboard', compact('stats', 'kpis', 'chartData'));
    }

    private function sumCustomerInvoices(string $type, Carbon $from, Carbon $to): float
    {
        return (float) CustomerInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('total_amount');
    }

    private function sumCustomerPaid(string $type, Carbon $from, Carbon $to): float
    {
        return (float) CustomerInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('paid_amount');
    }

    private function sumSupplierInvoices(string $type, Carbon $from, Carbon $to): float
    {
        return (float) SupplierInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('total_amount');
    }

    private function dailyCustomerSeries(Carbon $from, Carbon $to, string $type, string $column): array
    {
        $rows = CustomerInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date, SUM(' . $column . ') as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return $this->fillDaily($from, $to, $rows);
    }

    private function dailySupplierSeries(Carbon $from, Carbon $to, string $type): array
    {
        $rows = SupplierInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return $this->fillDaily($from, $to, $rows);
    }

    private function monthlyCustomerSeries(Carbon $from, Carbon $to, string $type, string $column): array
    {
        $rows = CustomerInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('YEAR(date) as y, MONTH(date) as m, SUM(' . $column . ') as total')
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', $row->y, $row->m))
            ->map(fn ($row) => $row->total);

        return $this->fillMonthly($from, $to, $rows);
    }

    private function monthlySupplierSeries(Carbon $from, Carbon $to, string $type): array
    {
        $rows = SupplierInvoice::where('type', $type)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('YEAR(date) as y, MONTH(date) as m, SUM(total_amount) as total')
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', $row->y, $row->m))
            ->map(fn ($row) => $row->total);

        return $this->fillMonthly($from, $to, $rows);
    }

    private function fillDaily(Carbon $from, Carbon $to, Collection $rows): array
    {
        $data = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $data[] = round((float) ($rows[$key] ?? 0), 2);
        }

        return $data;
    }

    private function fillMonthly(Carbon $from, Carbon $to, Collection $rows): array
    {
        $data = [];

        for ($date = $from->copy()->startOfMonth(); $date->lte($to); $date->addMonth()) {
            $key = $date->format('Y-m');
            $data[] = round((float) ($rows[$key] ?? 0), 2);
        }

        return $data;
    }

    private function dailyLabels(Carbon $from, Carbon $to): array
    {
        $labels = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $labels[] = $date->format('d/m');
        }

        return $labels;
    }

    private function monthlyLabels(Carbon $from, Carbon $to): array
    {
        $labels = [];
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        for ($date = $from->copy()->startOfMonth(); $date->lte($to); $date->addMonth()) {
            $labels[] = $months[(int) $date->format('n')] . ' ' . $date->format('Y');
        }

        return $labels;
    }

    private function paymentStatusBreakdown(Carbon $from, Carbon $to): array
    {
        $rows = CustomerInvoice::where('type', 'payment')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('state, COUNT(*) as count')
            ->groupBy('state')
            ->pluck('count', 'state');

        return [
            'labels' => ['مدفوع بالكامل', 'مدفوع جزئياً', 'غير مدفوع'],
            'data' => [
                (int) ($rows['paid'] ?? 0),
                (int) ($rows['partial'] ?? 0),
                (int) ($rows['unpaid'] ?? 0),
            ],
        ];
    }

    private function topProducts(Carbon $from, Carbon $to): array
    {
        $rows = CustomerInvoiceItems::query()
            ->join('customer_invoices', 'customer_invoice_items.customer_invoice_id', '=', 'customer_invoices.id')
            ->join('products', 'customer_invoice_items.product_id', '=', 'products.id')
            ->where('customer_invoices.type', 'payment')
            ->whereBetween('customer_invoices.date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('products.name, SUM(customer_invoice_items.quantity) as qty')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return [
            'labels' => $rows->pluck('name')->all(),
            'data' => $rows->pluck('qty')->map(fn ($q) => (int) $q)->all(),
        ];
    }
}
