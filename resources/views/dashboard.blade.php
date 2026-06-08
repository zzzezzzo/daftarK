@php
    $fmt = fn ($n) => number_format($n, 2);
@endphp

<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900">
    <div class="p-4 lg:p-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-1">
                    لوحة التحكم والتقارير
                </h1>
                <p class="text-gray-600 dark:text-gray-400">تقارير يومية وشهرية — {{ now()->translatedFormat('l j F Y') }}</p>
            </div>

            <div class="inline-flex rounded-xl border border-gray-200 dark:border-gray-600 p-1 bg-gray-100 dark:bg-gray-800 self-start">
                <button type="button" data-period="daily"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-blue-600 text-white shadow-md">
                    يومي
                </button>
                <button type="button" data-period="monthly"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-white text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    شهري
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-lg border-r-4 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">مبيعات اليوم</span>
                    <i class="bi bi-cart-check text-blue-500 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fmt($kpis['todaySales']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-gray-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthSales']) }} ج.م</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-lg border-r-4 border-green-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">المتحصل اليوم</span>
                    <i class="bi bi-cash-coin text-green-500 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fmt($kpis['todayCollected']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-gray-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthCollected']) }} ج.م</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-lg border-r-4 border-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">مشتريات اليوم</span>
                    <i class="bi bi-truck text-purple-500 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fmt($kpis['todayPurchases']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-gray-500 mt-1">هذا الشهر: {{ $fmt($kpis['monthPurchases']) }} ج.م</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-lg border-r-4 border-orange-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">صافي مبيعات الشهر</span>
                    <i class="bi bi-graph-up-arrow text-orange-500 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fmt($kpis['netMonthSales']) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-gray-500 mt-1">متبقي على العملاء: {{ $fmt($kpis['outstanding']) }} ج.م</p>
            </div>
        </div>

        {{-- Summary counts --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="bi bi-people-fill text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">العملاء</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['customers'] }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <i class="bi bi-truck text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الموردين</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['suppliers'] }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="bi bi-receipt text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">فواتير الشهر</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['monthInvoices'] }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <i class="bi bi-box-seam text-pink-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">المنتجات</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['products'] }}</p>
                </div>
            </div>
        </div>

        {{-- Charts row 1 --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">المبيعات والمرتجعات والتحصيل</h2>
                        <p id="periodHint" class="text-sm text-gray-500">آخر 30 يوم</p>
                    </div>
                    <i class="bi bi-bar-chart-line text-2xl text-blue-500"></i>
                </div>
                <div class="h-72">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">حالة الفواتير</h2>
                        <p class="text-sm text-gray-500">فواتير هذا الشهر</p>
                    </div>
                    <i class="bi bi-pie-chart text-2xl text-green-500"></i>
                </div>
                <div class="h-72">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Charts row 2 --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">المشتريات من الموردين</h2>
                        <p class="text-sm text-gray-500">حسب الفترة المختارة</p>
                    </div>
                    <i class="bi bi-basket text-2xl text-purple-500"></i>
                </div>
                <div class="h-64">
                    <canvas id="purchasesChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">أكثر المنتجات مبيعاً</h2>
                        <p class="text-sm text-gray-500">هذا الشهر — أعلى 5 منتجات</p>
                    </div>
                    <i class="bi bi-trophy text-2xl text-indigo-500"></i>
                </div>
                <div class="h-64">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly summary table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl mb-8">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">ملخص الشهر الحالي</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600 text-gray-500">
                            <th class="py-3 px-4 font-semibold">البند</th>
                            <th class="py-3 px-4 font-semibold">اليوم</th>
                            <th class="py-3 px-4 font-semibold">الشهر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr>
                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200">المبيعات</td>
                            <td class="py-3 px-4 tabular-nums">{{ $fmt($kpis['todaySales']) }} ج.م</td>
                            <td class="py-3 px-4 tabular-nums font-semibold text-blue-600">{{ $fmt($kpis['monthSales']) }} ج.م</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200">المرتجعات</td>
                            <td class="py-3 px-4 tabular-nums">{{ $fmt($kpis['todayReturns']) }} ج.م</td>
                            <td class="py-3 px-4 tabular-nums font-semibold text-red-600">{{ $fmt($kpis['monthReturns']) }} ج.م</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200">المتحصل</td>
                            <td class="py-3 px-4 tabular-nums">{{ $fmt($kpis['todayCollected']) }} ج.م</td>
                            <td class="py-3 px-4 tabular-nums font-semibold text-green-600">{{ $fmt($kpis['monthCollected']) }} ج.م</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200">المشتريات</td>
                            <td class="py-3 px-4 tabular-nums">{{ $fmt($kpis['todayPurchases']) }} ج.م</td>
                            <td class="py-3 px-4 tabular-nums font-semibold text-purple-600">{{ $fmt($kpis['monthPurchases']) }} ج.م</td>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200">صافي المبيعات</td>
                            <td class="py-3 px-4">—</td>
                            <td class="py-3 px-4 tabular-nums font-bold text-orange-600">{{ $fmt($kpis['netMonthSales']) }} ج.م</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">إجراءات سريعة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('customer.index') }}"
                   class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all shadow-md">
                    <i class="bi bi-cart-plus text-2xl"></i>
                    <span class="font-semibold">بيع جديد</span>
                </a>
                <a href="{{ route('customer.create') }}"
                   class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 transition-all shadow-md">
                    <i class="bi bi-person-plus text-2xl"></i>
                    <span class="font-semibold">إضافة عميل</span>
                </a>
                <a href="{{ route('suppliers.create') }}"
                   class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 transition-all shadow-md">
                    <i class="bi bi-truck text-2xl"></i>
                    <span class="font-semibold">إضافة مورد</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    window.dashboardChartData = @json($chartData);
</script>
@vite(['resources/js/dashboard.js'])
</x-app-layout>
