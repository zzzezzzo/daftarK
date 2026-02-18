<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900">
    <div class="p-4 lg:p-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                لوحة التحكم
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">مرحباً بك في نظام إدارة الفواتير</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Customers Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-blue-500">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-blue-500 text-white p-2 rounded-lg">
                        <i class="bi bi-people-fill text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">العملاء</h3>
                    <div class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                        +12%
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{$customerlength}}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">عميل نشط</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('customer.index') }}" 
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors">
                        عرض التفاصيل
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Suppliers Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-purple-500">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-purple-500 text-white p-2 rounded-lg">
                        <i class="bi bi-truck text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">الموردين</h3>
                    <div class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-medium">
                        +8%
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $supplierlegth }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">مورد نشط</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('suppliers.index') }}" 
                       class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors">
                        عرض التفاصيل
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Account Statements Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-green-500">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-green-500 text-white p-2 rounded-lg">
                        <i class="bi bi-journal-text text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">كشف الحسابات</h3>
                    <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium">
                        
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{$invoicelegth}}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">فاتورة هذا الشهر</p>
                </div>
                <div class="mt-4">
                    <a href="#" 
                       class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium text-sm transition-colors">
                        عرض التفاصيل
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Products Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border-l-4 border-orange-500">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-orange-500 text-white p-2 rounded-lg">
                        <i class="bi bi-box-seam text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">المخزون والمنتجات</h3>
                    <div class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium">
                        +25%
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $productlegth }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">منتج متاح</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 font-medium text-sm transition-colors">
                        عرض التفاصيل
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center">إجراءات سريعة</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="relative z-10">
                        <i class="bi bi-plus-circle text-3xl mb-3"></i>
                        <p class="text-lg font-semibold">بيع جديد</p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>

                <button class="group relative overflow-hidden bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="relative z-10">
                        <i class="bi bi-person-plus text-3xl mb-3"></i>
                        <p class="text-lg font-semibold">إضافة عميل</p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>

                <button class="group relative overflow-hidden bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="relative z-10">
                        <i class="bi bi-truck-plus text-3xl mb-3"></i>
                        <p class="text-lg font-semibold">إضافة مورد</p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>
    </div>
</div>
</x-app-layout>