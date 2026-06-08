<?php

namespace App\Services;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CustomerWhatsAppStatementService
{
    private const AR_MONTHS = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];

    public function buildMonthlyStatementUrl(Customer $customer, string $month): string
    {
        $message = $this->buildMonthlyStatementMessage($customer, $month);

        return 'https://wa.me/'.$this->normalizePhone($customer->phone).'?text='.rawurlencode($message);
    }

    public function buildMonthlyStatementMessage(Customer $customer, string $month): string
    {
        [$year, $monthNum] = array_map('intval', explode('-', $month));
        $start = Carbon::createFromDate($year, $monthNum, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $invoices = $customer->invoices()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get();

        $sales = $invoices->where('type', 'payment');
        $returns = $invoices->where('type', 'return');

        $monthLabel = (self::AR_MONTHS[$monthNum] ?? $monthNum).' '.$year;
        $totalAccountBalance = (float) $customer->invoices()
            ->where('type', 'payment')
            ->sum('remining_amount');

        $lines = [
            '*محل ابو يزيد*',
            'كشف حساب شهري',
            '━━━━━━━━━━━━━━',
            'العميل: '.$customer->name,
            'الفترة: '.$monthLabel,
            '',
        ];

        if ($invoices->isEmpty()) {
            $lines[] = 'لا توجد فواتير في هذه الفترة.';
        } else {
            $lines[] = '*تفاصيل الفواتير:*';
            $lines = array_merge($lines, $this->formatInvoiceLines($invoices));
            $lines[] = '';
            $lines[] = '*ملخص الشهر:*';
            $lines[] = '• المبيعات: '.$this->money($sales->sum('total_amount')).' ج.م ('.$sales->count().' فاتورة)';
            $lines[] = '• المرتجعات: '.$this->money($returns->sum('total_amount')).' ج.م ('.$returns->count().' فاتورة)';
            $lines[] = '• المتحصل: '.$this->money($sales->sum('paid_amount')).' ج.م';
            $lines[] = '• متبقي الشهر: '.$this->money($sales->sum('remining_amount')).' ج.م';
        }

        $lines[] = '';
        $lines[] = '*إجمالي المتبقي على الحساب:* '.$this->money($totalAccountBalance).' ج.م';

        if ($customer->wallet && $customer->type === 'permanent') {
            $lines[] = 'رصيد المحفظة: '.$this->money($customer->wallet->balance).' ج.م';
        }

        $lines[] = '';
        $lines[] = 'شكراً لتعاملكم معنا 🌹';

        return implode("\n", $lines);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if ($digits === '') {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '20'.substr($digits, 1);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '20'.$digits;
        }

        return $digits;
    }

    /**
     * @param  Collection<int, \App\Models\CustomerInvoice>  $invoices
     * @return list<string>
     */
    private function formatInvoiceLines(Collection $invoices): array
    {
        $lines = [];
        $maxLines = 15;
        $shown = $invoices->take($maxLines);

        foreach ($shown as $invoice) {
            $date = $invoice->date instanceof Carbon
                ? $invoice->date->format('Y-m-d')
                : Carbon::parse($invoice->date)->format('Y-m-d');

            $typeLabel = $invoice->type === 'return' ? 'مرتجع' : 'بيع';
            $lines[] = sprintf(
                '#%s | %s | %s | %s ج.م | متبقي: %s',
                $invoice->invoice_number,
                $date,
                $typeLabel,
                $this->money($invoice->total_amount),
                $this->money($invoice->remining_amount)
            );
        }

        $remaining = $invoices->count() - $shown->count();
        if ($remaining > 0) {
            $lines[] = '... و '.$remaining.' فاتورة أخرى';
        }

        return $lines;
    }

    private function money(float|int|string $amount): string
    {
        return number_format((float) $amount, 2);
    }
}
