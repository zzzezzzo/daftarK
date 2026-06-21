@php $fmt = fn ($n) => number_format($n, 2); @endphp

<x-app-layout>
<div class="page-shell">
    <div class="page-container">

        {{-- Header --}}
        <div class="ui-card overflow-hidden mb-6 animate-fade-in-up">
            <div class="ui-card-header">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                            <i class="bi bi-arrow-left-right text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">حركة المنتج</h1>
                            <p class="text-blue-100/90 text-sm">{{ $product->name }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('products.index') }}" class="ui-btn-ghost">
                            <i class="bi bi-arrow-right"></i> قائمة المنتجات
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}" class="ui-btn-ghost">
                            <i class="bi bi-pencil-square"></i> تعديل المنتج
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-slate-100 dark:border-slate-700">
                <div>
                    <p class="text-xs text-slate-500">الكود</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $product->code ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">الفئة</p>
                    <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $product->category?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">الرصيد الحالي</p>
                    <p class="font-bold text-brand-600 dark:text-brand-400 text-lg">{{ $product->stock }} <span class="text-xs font-normal">وحدة</span></p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">سعر الأساس</p>
                    <p class="font-semibold tabular-nums text-slate-700 dark:text-slate-200">{{ $fmt($product->price_base) }} ج.م</p>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3 mb-6 stagger-children">
            <div class="ui-kpi ui-kpi-blue !p-4">
                <p class="text-xs text-slate-500">مباع</p>
                <p class="text-xl font-bold text-rose-600 tabular-nums">−{{ $stats['sold'] }}</p>
            </div>
            <div class="ui-kpi ui-kpi-green !p-4">
                <p class="text-xs text-slate-500">مرتجع من عملاء</p>
                <p class="text-xl font-bold text-emerald-600 tabular-nums">+{{ $stats['returned_in'] }}</p>
            </div>
            <div class="ui-kpi ui-kpi-purple !p-4">
                <p class="text-xs text-slate-500">مشتريات</p>
                <p class="text-xl font-bold text-emerald-600 tabular-nums">+{{ $stats['purchased'] }}</p>
            </div>
            <div class="ui-kpi ui-kpi-orange !p-4">
                <p class="text-xs text-slate-500">مرتجع لموردين</p>
                <p class="text-xl font-bold text-rose-600 tabular-nums">−{{ $stats['returned_out'] }}</p>
            </div>
            <div class="ui-kpi ui-kpi-cyan !p-4">
                <p class="text-xs text-slate-500">صافي الحركة</p>
                <p class="text-xl font-bold tabular-nums {{ $stats['net_stock'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $stats['net_stock'] >= 0 ? '+' : '' }}{{ $stats['net_stock'] }}
                </p>
            </div>
            <div class="ui-kpi ui-kpi-pink !p-4">
                <p class="text-xs text-slate-500">عدد الحركات</p>
                <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['count'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="ui-card p-5 mb-6 animate-fade-in-up">
            <form method="GET" action="{{ route('products.movements', $product->id) }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">
                <div class="relative xl:col-span-3">
                    <i class="bi bi-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث: عميل، مورد، فاتورة..." class="ui-input pr-12">
                </div>
                <select name="type" class="ui-input xl:col-span-2">
                    <option value="">كل الحركات</option>
                    <option value="sale" @selected(request('type') === 'sale')>بيع لعميل</option>
                    <option value="return_in" @selected(request('type') === 'return_in')>مرتجع من عميل</option>
                    <option value="purchase" @selected(request('type') === 'purchase')>شراء من مورد</option>
                    <option value="return_out" @selected(request('type') === 'return_out')>مرتجع لمورد</option>
                </select>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="ui-input xl:col-span-2" title="من تاريخ">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="ui-input xl:col-span-2" title="إلى تاريخ">
                <button type="submit" class="ui-btn-primary xl:col-span-1"><i class="bi bi-funnel"></i> فلتر</button>
                <a href="{{ route('products.movements', $product->id) }}" class="ui-btn xl:col-span-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-center justify-center">
                    <i class="bi bi-arrow-counterclockwise"></i> إعادة
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="ui-card overflow-hidden animate-fade-in-up">
            <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-700/80">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-list-ul text-brand-500"></i>
                    سجل الحركات
                </h2>
            </div>

            @if($movements->isEmpty())
                <div class="p-12 text-center">
                    <i class="bi bi-inbox text-4xl text-slate-300 dark:text-slate-600"></i>
                    <p class="mt-3 text-slate-500">لا توجد حركات لهذا المنتج{{ request()->anyFilled(['search','type','from_date','to_date']) ? ' ضمن الفلتر المحدد' : '' }}</p>
                </div>
            @else
                <div class="ui-table-wrap border-0 rounded-none">
                    <table class="ui-table w-full text-sm">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>نوع الحركة</th>
                                <th>الطرف</th>
                                <th>رقم الفاتورة</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-center">تأثير المخزون</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $row)
                            @php
                                $typeColors = [
                                    'sale' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                    'return_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                                    'purchase' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                    'return_out' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300',
                                ];
                                $invoiceUrl = $row['party_type'] === 'customer'
                                    ? route('customerAccountStatement.show', [$row['party_id'], $row['invoice_id']])
                                    : route('accountStatement.show', [$row['party_id'], $row['invoice_id']]);
                            @endphp
                            <tr>
                                <td class="tabular-nums text-slate-600 dark:text-slate-400">{{ $row['date'] }}</td>
                                <td>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeColors[$row['type']] ?? '' }}">
                                        {{ $row['type_label'] }}
                                    </span>
                                </td>
                                <td class="font-medium">
                                    @if($row['party_type'] === 'customer')
                                        <i class="bi bi-person text-emerald-500 text-xs"></i>
                                    @else
                                        <i class="bi bi-truck text-amber-500 text-xs"></i>
                                    @endif
                                    {{ $row['party'] }}
                                </td>
                                <td>
                                    <a href="{{ $invoiceUrl }}" target="_blank" rel="noopener"
                                       class="text-brand-600 dark:text-brand-400 hover:underline font-semibold">
                                        {{ $row['invoice_number'] }}
                                    </a>
                                </td>
                                <td class="text-center tabular-nums font-semibold">{{ $row['quantity'] }}</td>
                                <td class="text-center tabular-nums font-bold {{ $row['stock_effect'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $row['stock_effect'] >= 0 ? '+' : '' }}{{ $row['stock_effect'] }}
                                </td>
                                <td class="tabular-nums">{{ $fmt($row['unit_price']) }}</td>
                                <td class="tabular-nums font-semibold">{{ $fmt($row['line_total']) }} ج.م</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700">
                            <tr>
                                <td colspan="5" class="font-semibold text-slate-700 dark:text-slate-200">الإجمالي (الحركات المعروضة)</td>
                                <td class="text-center font-bold tabular-nums {{ $stats['net_stock'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $stats['net_stock'] >= 0 ? '+' : '' }}{{ $stats['net_stock'] }}
                                </td>
                                <td></td>
                                <td class="font-bold tabular-nums">{{ $fmt($stats['total_value']) }} ج.م</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
</x-app-layout>
