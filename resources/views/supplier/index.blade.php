<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-truck text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">الموردين</h1>
                                <p class="text-yellow-100 text-sm">إدارة جميع الموردين</p>
                            </div>
                        </div>
                        <a href="{{ route('suppliers.create') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>مورد جديد</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('suppliers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن مورد..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <button 
                        type="submit" 
                        class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-3 rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all font-medium">
                        <i class="bi bi-search ml-2"></i>
                        بحث
                    </button>
                </form>
            </div>

            <!-- Suppliers Grid -->
            @if($suppliers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($suppliers as $supplier)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                            <i class="bi bi-building text-lg"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white truncate">{{ $supplier->name }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-6">
                                <!-- Contact Info -->
                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-telephone text-yellow-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">رقم الهاتف</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $supplier->phone }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($supplier->email)
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                                <i class="bi bi-envelope text-blue-600 text-sm"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">البريد الإلكتروني</p>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $supplier->email }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($supplier->address)
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                                <i class="bi bi-geo-alt text-green-600 text-sm"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">العنوان</p>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $supplier->address }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Balance Summary -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 mb-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-cash-stack text-orange-600"></i>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الرصيد</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-orange-600 dark:text-orange-400">
                                                {{ number_format($supplier->invoices->sum('Remaining_amount'), 2) }} ج.م
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('accountStatement.index', $supplier->id) }}" 
                                       class="flex-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg hover:bg-green-200 dark:hover:bg-green-800/30 transition-colors text-center flex items-center justify-center gap-1">
                                        <i class="bi bi-receipt text-sm"></i>
                                        <span class="text-sm">كشف</span>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                       class="flex-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-3 py-2 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors text-center flex items-center justify-center gap-1">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                        <span class="text-sm">تعديل</span>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف المورد: {{ $supplier->name }}؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                               class="flex-1 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors flex items-center justify-center gap-1">
                                            <i class="bi bi-trash3 text-sm"></i>
                                            <span class="text-sm">حذف</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-truck text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">لا يوجد موردين</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">قم بإضافة مورد جديد لبدء إدارة الموردين</p>
                    <a href="{{ route('suppliers.create') }}" 
                       class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-3 rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all inline-flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>إضافة مورد جديد</span>
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if($suppliers->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
