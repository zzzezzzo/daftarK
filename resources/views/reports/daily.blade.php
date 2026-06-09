@php
    $fmt = fn ($n) => number_format($n, 2);
    $s = $report['summary'];
@endphp

<x-app-layout>
<div class="page-shell">
    <div class="page-container">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 animate-fade-in-up">
            <div>
                <h1 class="ui-page-title">التقرير اليومي للحركة المالية</h1>
                <p class="ui-page-subtitle mt-1">{{ $report['dateLabel'] }} — كل ما دخل وخرج من الخزينة والحسابات</p>
            </div>
            <a href="{{ route('dashboard') }}" class="ui-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600">
                <i class="bi bi-arrow-right"></i> لوحة التحكم
            </a>
        </div>

        {{-- Filters --}}
        <div class="ui-card p-5 mb-6 animate-fade-in-up" style="animation-delay:0.04s">
            <form method="GET" action="{{ route('reports.daily') }}" class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">التاريخ</label>
                    <input type="date" name="date" value="{{ $date->toDateString() }}" class="ui-input">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">الصندوق</label>
                    <select name="cash_box_id" class="ui-input">
                        <option value="">كل الصناديق</option>
                        @foreach($cashBoxes as $box)
                            <option value="{{ $box->id }}" @selected($cashBoxId == $box->id)>{{ $box->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="ui-btn-primary px-8">
                    <i class="bi bi-funnel"></i> عرض التقرير
                </button>
                <a href="{{ route('reports.daily', ['date' => now()->toDateString()]) }}" class="ui-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    اليوم
                </a>
            </form>
        </div>

        {{-- Summary KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6 stagger-children">
            <div class="ui-kpi ui-kpi-green">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الوارد (خزينة)</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $fmt($s['total_in']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">{{ $s['in_count'] }} حركة داخلة</p>
            </div>
            <div class="ui-kpi ui-kpi-pink">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الصادر (خزينة)</p>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 tabular-nums">{{ $fmt($s['total_out']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">{{ $s['out_count'] }} حركة خارجة</p>
            </div>
            <div class="ui-kpi ui-kpi-blue">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">صافي الحركة النقدية</p>
                <p class="text-2xl font-bold tabular-nums {{ $s['net'] >= 0 ? 'text-brand-600' : 'text-rose-600' }}">{{ $fmt($s['net']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">وارد − صادر</p>
            </div>
            <div class="ui-kpi ui-kpi-purple">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">حركات الحسابات</p>
                <p class="text-2xl font-bold text-violet-600 dark:text-violet-400 tabular-nums">{{ $s['account_count'] }}</p>
                <p class="text-xs text-slate-500 mt-1">عملاء وموردين</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            {{-- Incoming --}}
            <div class="ui-card overflow-hidden animate-fade-in-up">
                <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-700/80 bg-emerald-50/50 dark:bg-emerald-500/10">
                    <h2 class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                        <i class="bi bi-arrow-down-circle-fill"></i> الوارد — دخل فين؟
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">من دفع ← إلى أي صندوق</p>
                </div>
                @if($report['incoming']->isEmpty())
                    <p class="p-6 text-center text-slate-500">لا توجد حركات واردة في هذا اليوم</p>
                @else
                    <div class="ui-table-wrap border-0 rounded-none max-h-[28rem] overflow-y-auto">
                        <table class="ui-table w-full text-sm">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th>الوقت</th>
                                    <th>من</th>
                                    <th>إلى (الصندوق)</th>
                                    <th>التصنيف</th>
                                    <th class="text-left">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['incoming'] as $row)
                                <tr>
                                    <td class="tabular-nums text-slate-500">{{ $row['time'] }}</td>
                                    <td class="font-medium">{{ $row['from'] }}</td>
                                    <td>{{ $row['to'] }}</td>
                                    <td><span class="px-2 py-0.5 rounded-lg text-xs bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">{{ $row['category'] }}</span></td>
                                    <td class="text-left font-bold text-emerald-600 tabular-nums">+{{ $fmt($row['amount']) }}</td>
                                </tr>
                                @if($row['description'])
                                <tr class="!bg-transparent">
                                    <td colspan="5" class="!pt-0 !pb-3 text-xs text-slate-400 pr-4">{{ $row['description'] }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Outgoing --}}
            <div class="ui-card overflow-hidden animate-fade-in-up" style="animation-delay:0.06s">
                <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-700/80 bg-rose-50/50 dark:bg-rose-500/10">
                    <h2 class="font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                        <i class="bi bi-arrow-up-circle-fill"></i> الصادر — خرج راح فين؟
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">من الصندوق ← إلى مين / فين</p>
                </div>
                @if($report['outgoing']->isEmpty())
                    <p class="p-6 text-center text-slate-500">لا توجد حركات صادرة في هذا اليوم</p>
                @else
                    <div class="ui-table-wrap border-0 rounded-none max-h-[28rem] overflow-y-auto">
                        <table class="ui-table w-full text-sm">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th>الوقت</th>
                                    <th>من (الصندوق)</th>
                                    <th>إلى</th>
                                    <th>التصنيف</th>
                                    <th class="text-left">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['outgoing'] as $row)
                                <tr>
                                    <td class="tabular-nums text-slate-500">{{ $row['time'] }}</td>
                                    <td>{{ $row['from'] }}</td>
                                    <td class="font-medium">{{ $row['to'] }}</td>
                                    <td><span class="px-2 py-0.5 rounded-lg text-xs bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300">{{ $row['category'] }}</span></td>
                                    <td class="text-left font-bold text-rose-600 tabular-nums">−{{ $fmt($row['amount']) }}</td>
                                </tr>
                                @if($row['description'])
                                <tr class="!bg-transparent">
                                    <td colspan="5" class="!pt-0 !pb-3 text-xs text-slate-400 pr-4">{{ $row['description'] }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Category breakdown --}}
        @if($report['by_category_in']->isNotEmpty() || $report['by_category_out']->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="ui-card p-5">
                <h3 class="font-semibold text-slate-800 dark:text-white mb-3">ملخص الوارد حسب النوع</h3>
                <div class="space-y-2">
                    @forelse($report['by_category_in'] as $item)
                    <div class="flex justify-between items-center text-sm py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        <span class="text-slate-600 dark:text-slate-300">{{ $item['category'] }} <span class="text-xs text-slate-400">({{ $item['count'] }})</span></span>
                        <span class="font-bold text-emerald-600 tabular-nums">{{ $fmt($item['total']) }} ج.م</span>
                    </div>
                    @empty
                    <p class="text-slate-500 text-sm">—</p>
                    @endforelse
                </div>
            </div>
            <div class="ui-card p-5">
                <h3 class="font-semibold text-slate-800 dark:text-white mb-3">ملخص الصادر حسب النوع</h3>
                <div class="space-y-2">
                    @forelse($report['by_category_out'] as $item)
                    <div class="flex justify-between items-center text-sm py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        <span class="text-slate-600 dark:text-slate-300">{{ $item['category'] }} <span class="text-xs text-slate-400">({{ $item['count'] }})</span></span>
                        <span class="font-bold text-rose-600 tabular-nums">{{ $fmt($item['total']) }} ج.م</span>
                    </div>
                    @empty
                    <p class="text-slate-500 text-sm">—</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- Account movements --}}
        <div class="ui-card overflow-hidden mb-6 animate-fade-in-up">
            <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-700/80">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-journal-text text-brand-500"></i>
                    حركات حسابات العملاء والموردين
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">دفعات، مبيعات، مرتجعات — على حساب مين</p>
            </div>
            @if($report['account_movements']->isEmpty())
                <p class="p-6 text-center text-slate-500">لا توجد حركات حسابات في هذا اليوم</p>
            @else
                <div class="ui-table-wrap border-0 rounded-none">
                    <table class="ui-table w-full text-sm">
                        <thead>
                            <tr>
                                <th>الوقت</th>
                                <th>الحساب</th>
                                <th>النوع</th>
                                <th>الطريقة</th>
                                <th>البيان</th>
                                <th class="text-left">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['account_movements'] as $row)
                            <tr>
                                <td class="tabular-nums text-slate-500">{{ $row['time'] }}</td>
                                <td class="font-medium">
                                    @if($row['party_type'] === 'customer')
                                        <i class="bi bi-person text-emerald-500 text-xs"></i>
                                    @else
                                        <i class="bi bi-truck text-amber-500 text-xs"></i>
                                    @endif
                                    {{ $row['account'] }}
                                </td>
                                <td>{{ $row['type'] }}</td>
                                <td>{{ $row['method'] }}</td>
                                <td class="text-slate-500 text-xs max-w-[200px] truncate" title="{{ $row['description'] }}">{{ $row['description'] }}</td>
                                <td class="text-left font-bold tabular-nums {{ $row['direction'] === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $row['direction'] === 'in' ? '+' : '−' }}{{ $fmt($row['amount']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="text-center print:hidden">
            <button type="button" onclick="window.print()" class="ui-btn-primary">
                <i class="bi bi-printer"></i> طباعة التقرير
            </button>
        </div>

    </div>
</div>
</x-app-layout>
