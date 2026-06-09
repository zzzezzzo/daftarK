<?php

namespace App\Services;

use App\Models\CashBoxTransaction;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerTransaction;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DailyCashFlowReportService
{
    public function build(Carbon $date, ?int $cashBoxId = null): array
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $cashQuery = CashBoxTransaction::with(['cashBox', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at');

        if ($cashBoxId) {
            $cashQuery->where('cash_box_id', $cashBoxId);
        }

        $cashTransactions = $cashQuery->get()->map(fn ($tx) => $this->mapCashTransaction($tx));

        $incoming = $cashTransactions->where('direction', 'in')->values();
        $outgoing = $cashTransactions->where('direction', 'out')->values();

        $accountMovements = $this->buildAccountMovements($start, $end);

        return [
            'date' => $date->toDateString(),
            'dateLabel' => $date->translatedFormat('l j F Y'),
            'summary' => [
                'total_in' => round((float) $incoming->sum('amount'), 2),
                'total_out' => round((float) $outgoing->sum('amount'), 2),
                'net' => round((float) $incoming->sum('amount') - (float) $outgoing->sum('amount'), 2),
                'in_count' => $incoming->count(),
                'out_count' => $outgoing->count(),
                'account_count' => $accountMovements->count(),
            ],
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'account_movements' => $accountMovements,
            'by_category_in' => $this->groupByCategory($incoming),
            'by_category_out' => $this->groupByCategory($outgoing),
        ];
    }

    private function mapCashTransaction(CashBoxTransaction $tx): array
    {
        $party = $this->resolveParty($tx);
        $category = $this->categoryLabel($tx->reference_type, $tx->type);

        $from = $tx->type === 'in' ? $party['name'] : ($tx->cashBox?->name ?? 'الخزينة');
        $to = $tx->type === 'in' ? ($tx->cashBox?->name ?? 'الخزينة') : $party['name'];

        return [
            'id' => $tx->id,
            'time' => $this->toCarbon($tx->created_at)->format('H:i'),
            'datetime' => $this->toCarbon($tx->created_at)->format('Y-m-d H:i'),
            'direction' => $tx->type,
            'amount' => (float) $tx->amount,
            'description' => $tx->description,
            'category' => $category,
            'category_key' => $tx->reference_type ?? 'other',
            'from' => $from,
            'to' => $to,
            'party' => $party['name'],
            'party_type' => $party['type'],
            'cash_box' => $tx->cashBox?->name ?? '—',
            'user' => $tx->user?->name ?? '—',
            'reference_type' => $tx->reference_type,
            'reference_id' => $tx->reference_id,
        ];
    }

    /**
     * @return array{name: string, type: string}
     */
    private function resolveParty(CashBoxTransaction $tx): array
    {
        if ($tx->reference_type === 'customer_invoice' && $tx->reference_id) {
            $invoice = CustomerInvoice::with('customer')->find($tx->reference_id);
            if ($invoice?->customer) {
                return [
                    'name' => 'عميل: '.$invoice->customer->name,
                    'type' => 'customer',
                ];
            }
        }

        if ($tx->reference_type === 'supplier_invoice' && $tx->reference_id) {
            $invoice = SupplierInvoice::with('supplier')->find($tx->reference_id);
            if ($invoice?->supplier) {
                return [
                    'name' => 'مورد: '.$invoice->supplier->name,
                    'type' => 'supplier',
                ];
            }
        }

        if ($tx->reference_type === 'treasury_payment' && $tx->description) {
            return [
                'name' => $this->extractNameFromDescription($tx->description, 'العميل') ?: 'عميل',
                'type' => 'customer',
            ];
        }

        if ($tx->reference_type === 'manual' && $tx->reference_id) {
            $supplier = Supplier::find($tx->reference_id);
            if ($supplier) {
                return ['name' => 'مورد: '.$supplier->name, 'type' => 'supplier'];
            }
        }

        if ($tx->reference_type === 'opening') {
            return ['name' => 'رصيد افتتاحي', 'type' => 'system'];
        }

        if ($tx->reference_type === 'manual') {
            return ['name' => 'حركة يدوية', 'type' => 'manual'];
        }

        return ['name' => $tx->description ?: '—', 'type' => 'other'];
    }

    private function extractNameFromDescription(string $description, string $keyword): ?string
    {
        if (preg_match('/'.$keyword.'\s+(.+?)(?:\s+فاتورة|$)/u', $description, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function categoryLabel(?string $referenceType, string $direction): string
    {
        return match ($referenceType) {
            'customer_invoice' => $direction === 'in' ? 'تحصيل من عميل' : 'دفعة لعميل',
            'supplier_invoice' => 'دفعة لمورد (فاتورة)',
            'treasury_payment' => 'سحب لصالح عميل',
            'opening' => 'رصيد افتتاحي',
            'manual' => $direction === 'in' ? 'إيداع يدوي' : 'صرف يدوي / دفعة',
            default => $direction === 'in' ? 'وارد نقدي' : 'صادر نقدي',
        };
    }

    private function buildAccountMovements(Carbon $start, Carbon $end): Collection
    {
        $customerTx = CustomerTransaction::with('customer')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('transaction_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->whereNull('transaction_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderBy('created_at')
            ->get()
            ->map(fn ($tx) => [
                'time' => $this->toCarbon($tx->getRawOriginal('transaction_date') ?? $tx->created_at)->format('H:i'),
                'party' => $tx->customer?->name ?? '—',
                'party_type' => 'customer',
                'account' => 'حساب عميل: '.($tx->customer?->name ?? '—'),
                'type' => $this->customerTypeLabel($tx->type),
                'type_key' => $tx->type,
                'direction' => in_array($tx->type, ['payment', 'deposit']) ? 'in' : 'out',
                'amount' => (float) $tx->amount,
                'method' => $this->methodLabel($tx->method ?? null),
                'description' => $tx->description ?? '—',
            ]);

        $supplierTx = SupplierTransaction::with('supplier')
            ->whereBetween('transaction_date', [$start, $end])
            ->orderBy('transaction_date')
            ->get()
            ->map(fn ($tx) => [
                'time' => $this->toCarbon($tx->transaction_date ?? $tx->created_at)->format('H:i'),
                'party' => $tx->supplier?->name ?? '—',
                'party_type' => 'supplier',
                'account' => 'حساب مورد: '.($tx->supplier?->name ?? '—'),
                'type' => $tx->type === 'deposit' ? 'إيداع' : 'سحب / دفعة',
                'type_key' => $tx->type,
                'direction' => $tx->type === 'deposit' ? 'in' : 'out',
                'amount' => (float) $tx->amount,
                'method' => '—',
                'description' => $tx->description ?? '—',
            ]);

        return $customerTx->concat($supplierTx)->sortBy('time')->values();
    }

    private function customerTypeLabel(string $type): string
    {
        return match ($type) {
            'payment' => 'دفعة',
            'sale' => 'مبيعة',
            'return' => 'مرتجع',
            'adjustment' => 'تسوية',
            'deposit' => 'إيداع',
            'invoice' => 'فاتورة',
            default => $type,
        };
    }

    private function methodLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'نقدي',
            'wallet' => 'محفظة',
            'bank' => 'بنك',
            default => $method ? $method : '—',
        };
    }

    private function groupByCategory(Collection $items): Collection
    {
        return $items->groupBy('category')->map(function ($group, $category) {
            return [
                'category' => $category,
                'count' => $group->count(),
                'total' => round((float) $group->sum('amount'), 2),
            ];
        })->values()->sortByDesc('total')->values();
    }

    private function toCarbon(mixed $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        return Carbon::parse((string) $value);
    }
}
