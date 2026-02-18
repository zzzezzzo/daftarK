<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-people text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">العملاء</h1>
                                <p class="text-blue-100 text-sm">إدارة بيانات العملاء</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.create') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-person-plus"></i>
                            <span>عميل جديد</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('customer.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن عميل بالاسم أو رقم الهاتف..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">إجمالي العملاء</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customers->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-people text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">العملاء الدائمون</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customers->where('type', 'permanent')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-person-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">العملاء العابرين</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customers->where('type', 'walkin')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-person-walking text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-person text-blue-600"></i>
                                        اسم العميل
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-telephone text-blue-600"></i>
                                        رقم الهاتف
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-person-badge text-blue-600"></i>
                                        نوع العميل
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-tag text-blue-600"></i>
                                        نوع التسعير
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-gear text-blue-600"></i>
                                        إجراءات
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-person text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-white">{{ $customer->name }}</p>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $customer->type == 'permanent' ? 'عميل دائم' : 'عميل عابر' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-telephone text-gray-400"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $customer->phone }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        @if($customer->type == 'permanent')
                                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                                <i class="bi bi-person-check"></i> دائم
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-full text-sm font-medium">
                                                <i class="bi bi-person-walking"></i> عابر
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        @switch($customer->price_type)
                                            @case('client')
                                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                                    <i class="bi bi-person"></i> عملاء
                                                </span>
                                                @break
                                            @case('trade')
                                                <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">
                                                    <i class="bi bi-shop"></i> تجار
                                                </span>
                                                @break
                                            @case('technical')
                                                <span class="px-3 py-1 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-full text-sm font-medium">
                                                    <i class="bi bi-tools"></i> فنيين
                                                </span>
                                                @break
                                            @default
                                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">
                                                    <i class="bi bi-dash"></i> غير محدد
                                                </span>
                                        @endswitch
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('customerAccountStatement.index', $customer->id) }}" 
                                           class="p-2 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-800/30 transition-colors"
                                           title="كشف الحساب (فواتير)">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                        <a href="{{ route('customerAccountStatement.transactionIndex', $customer->id) }}" 
                                           class="p-2 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-800/30 transition-colors"
                                           title="المعاملات">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </a>
                                        <a href="{{ route('customerAccountStatement.create', $customer->id) }}" 
                                           class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                           title="إنشاء فاتورة">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                        <a href="{{ route('customer.edit', $customer->id) }}" 
                                           class="p-2 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800/30 transition-colors"
                                           title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('customer.destroy', $customer->id) }}"
                                              method="POST"
                                              class="inline delete-customer"
                                              data-id="{{ $customer->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($customers->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $customers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <script>
    </script>
</x-app-layout>
