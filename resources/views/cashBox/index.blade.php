<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-cash-stack text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">الصناديق النقدية</h1>
                                <p class="text-green-100 text-sm">إدارة جميع صناديق النقدية</p>
                            </div>
                        </div>
                        <a href="{{ route('cashBoxes.create') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>صندوق جديد</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cash Boxes Grid -->
            @if($cashBoxes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cashBoxes as $cashBox)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow">
                            <!-- Header -->
                            <div class="bg-gradient-to-r {{ $cashBox->status === 'active' ? 'from-green-500 to-teal-500' : 'from-gray-500 to-gray-600' }} p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-white">{{ $cashBox->name }}</h3>
                                    <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium text-white">
                                        {{ $cashBox->status === 'active' ? 'نشط' : 'مغلق' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Balance -->
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">الرصيد الحالي</p>
                                    <p class="text-3xl font-bold {{ $cashBox->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $cashBox->balance_formatted }}
                                    </p>
                                </div>
                                
                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي الداخل</p>
                                        <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ number_format($cashBox->total_in, 2) }} ج.م</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي الخارج</p>
                                        <p class="text-sm font-semibold text-red-600 dark:text-red-400">{{ number_format($cashBox->total_out, 2) }} ج.م</p>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('cashBoxes.show', $cashBox->id) }}" 
                                       class="flex-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-3 py-2 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors text-center">
                                        <i class="bi bi-eye"></i>
                                        <span class="text-sm">عرض</span>
                                    </a>
                                    
                                    @if($cashBox->status === 'active')
                                        <form action="{{ route('cashBoxes.close', $cashBox->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    onclick="return confirm('هل أنت متأكد من إغلاق هذا الصندوق؟')"
                                                    class="w-full bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors text-center">
                                                <i class="bi bi-lock"></i>
                                                <span class="text-sm">إغلاق</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('cashBoxes.reopen', $cashBox->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    onclick="return confirm('هل أنت متأكد من إعادة فتح هذا الصندوق؟')"
                                                    class="w-full bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg hover:bg-green-200 dark:hover:bg-green-800/30 transition-colors text-center">
                                                <i class="bi bi-unlock"></i>
                                                <span class="text-sm">إعادة فتح</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-cash-stack text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">لا توجد صناديق نقدية</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">قم بإنشاء صندوق نقدي جديد لبدء إدارة الأموال</p>
                    <a href="{{ route('cashBoxes.create') }}" 
                       class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors inline-flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>إنشاء صندوق جديد</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
