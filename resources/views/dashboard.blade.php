@php $fmt = fn ($n) => number_format($n, 2); @endphp

<x-app-layout>
<div class="page-shell">
    <div class="page-container">

        <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="ui-page-title">لوحة التحكم والتقارير</h1>
                <p class="ui-page-subtitle mt-1">تقارير يومية وشهرية — {{ now()->translatedFormat('l j F Y') }}</p>
            </div>
            <div class="ui-segment self-start">
                <button type="button" data-period="daily" class="ui-segment-btn ui-segment-btn-active">يومي</button>
                <button type="button" data-period="monthly" class="ui-segment-btn">شهري</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6 stagger-children">
            <div class="ui-kpi ui-kpi-blue">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">مبيعات اليوم</span>
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i class="bi bi-cart-check text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $fmt($kpis['todaySales']) }} <span class="text-sm font-normal text-slate-500">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthSales']) }} ج.م</p>
            </div>

            <div class="ui-kpi ui-kpi-green">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">المتحصل اليوم</span>
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="bi bi-cash-coin text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $fmt($kpis['todayCollected']) }} <span class="text-sm font-normal text-slate-500">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthCollected']) }} ج.م</p>
            </div>

            <div class="ui-kpi ui-kpi-purple">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">مشتريات اليوم</span>
                    <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center">
                        <i class="bi bi-truck text-violet-600 dark:text-violet-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $fmt($kpis['todayPurchases']) }} <span class="text-sm font-normal text-slate-500">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthPurchases']) }} ج.م</p>
            </div>

            <div class="ui-kpi ui-kpi-orange">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">صافي مبيعات الشهر</span>
                    <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                        <i class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $fmt($kpis['netMonthSales']) }} <span class="text-sm font-normal text-slate-500">ج.م</span></p>
                <p class="text-xs text-slate-500 mt-1">متبقي: {{ $fmt($kpis['outstanding']) }} ج.م</p>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 stagger-children">
            <div class="ui-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center"><i class="bi bi-people-fill text-blue-600 dark:text-blue-400"></i></div>
                <div><p class="text-xs text-slate-500">العملاء</p><p class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['customers'] }}</p></div>
            </div>
            <div class="ui-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center"><i class="bi bi-truck text-amber-600 dark:text-amber-400"></i></div>
                <div><p class="text-xs text-slate-500">الموردين</p><p class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['suppliers'] }}</p></div>
            </div>
            <div class="ui-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center"><i class="bi bi-receipt text-emerald-600 dark:text-emerald-400"></i></div>
                <div><p class="text-xs text-slate-500">فواتير الشهر</p><p class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['monthInvoices'] }}</p></div>
            </div>
            <div class="ui-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center"><i class="bi bi-box-seam text-pink-600 dark:text-pink-400"></i></div>
                <div><p class="text-xs text-slate-500">المنتجات</p><p class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['products'] }}</p></div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <div class="xl:col-span-2 ui-card p-6 animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">المبيعات والمرتجعات والتحصيل</h2>
                        <p id="periodHint" class="text-sm text-slate-500">آخر 30 يوم</p>
                    </div>
                    <i class="bi bi-bar-chart-line text-2xl text-brand-500"></i>
                </div>
                <div class="h-72"><canvas id="salesChart"></canvas></div>
            </div>
            <div class="ui-card p-6 animate-fade-in-up" style="animation-delay:0.08s">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">حالة الفواتير</h2>
                        <p class="text-sm text-slate-500">فواتير هذا الشهر</p>
                    </div>
                    <i class="bi bi-pie-chart text-2xl text-emerald-500"></i>
                </div>
                <div class="h-72"><canvas id="paymentStatusChart"></canvas></div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            <div class="ui-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">المشتريات من الموردين</h2>
                    <i class="bi bi-basket text-2xl text-violet-500"></i>
                </div>
                <div class="h-64"><canvas id="purchasesChart"></canvas></div>
            </div>
            <div class="ui-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">أكثر المنتجات مبيعاً</h2>
                    <i class="bi bi-trophy text-2xl text-indigo-500"></i>
                </div>
                <div class="h-64"><canvas id="topProductsChart"></canvas></div>
            </div>
        </div>

        <div class="ui-card p-6 mb-8">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4">ملخص الشهر الحالي</h2>
            <div class="ui-table-wrap">
                <table class="ui-table w-full text-sm">
                    <thead>
                        <tr>
                            <th>البند</th><th>اليوم</th><th>الشهر</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="font-medium">المبيعات</td><td class="tabular-nums">{{ $fmt($kpis['todaySales']) }} ج.م</td><td class="tabular-nums font-semibold text-brand-600">{{ $fmt($kpis['monthSales']) }} ج.م</td></tr>
                        <tr><td class="font-medium">المرتجعات</td><td class="tabular-nums">{{ $fmt($kpis['todayReturns']) }} ج.م</td><td class="tabular-nums font-semibold text-red-500">{{ $fmt($kpis['monthReturns']) }} ج.م</td></tr>
                        <tr><td class="font-medium">المتحصل</td><td class="tabular-nums">{{ $fmt($kpis['todayCollected']) }} ج.م</td><td class="tabular-nums font-semibold text-emerald-600">{{ $fmt($kpis['monthCollected']) }} ج.م</td></tr>
                        <tr><td class="font-medium">المشتريات</td><td class="tabular-nums">{{ $fmt($kpis['todayPurchases']) }} ج.م</td><td class="tabular-nums font-semibold text-violet-600">{{ $fmt($kpis['monthPurchases']) }} ج.م</td></tr>
                        <tr class="bg-slate-50 dark:bg-slate-800/50"><td class="font-bold">صافي المبيعات</td><td>—</td><td class="tabular-nums font-bold text-orange-500">{{ $fmt($kpis['netMonthSales']) }} ج.م</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ui-card p-6 mb-8">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4">إجراءات سريعة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger-children">
                <a href="{{ route('reports.daily') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-l from-cyan-600 to-teal-600 text-white font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 active:scale-[0.98]">
                    <i class="bi bi-calendar-day text-xl"></i><span>التقرير اليومي</span>
                </a>
                <a href="{{ route('customer.index') }}" class="ui-btn-primary p-4 !justify-start hover:-translate-y-0.5">
                    <i class="bi bi-cart-plus text-xl"></i><span>بيع جديد</span>
                </a>
                <a href="{{ route('customer.create') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-l from-violet-600 to-purple-600 text-white font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 active:scale-[0.98]">
                    <i class="bi bi-person-plus text-xl"></i><span>إضافة عميل</span>
                </a>
                <a href="{{ route('suppliers.create') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-l from-orange-500 to-amber-600 text-white font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 active:scale-[0.98]">
                    <i class="bi bi-truck text-xl"></i><span>إضافة مورد</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>window.dashboardChartData = @json($chartData);</script>
@vite(['resources/js/dashboard.js'])
</x-app-layout>
