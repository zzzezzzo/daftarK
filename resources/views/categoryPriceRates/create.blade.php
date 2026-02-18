<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-percent text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">أسعار الفئة</h1>
                                <p class="text-green-100 text-sm">تحديد نسب التسعير للفئة: {{ $category->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('categories.index') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i>
                            <span>رجوع</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl mb-6 flex items-start gap-3">
                    <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-exclamation-triangle text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold mb-2">يرجى تصحيح الأخطاء التالية:</p>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-dot text-red-500"></i>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form action="{{ route('categoryPriceRates.store', $category->id) }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Current Rates Info -->
                    @if($priceRate)
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-xl border border-blue-200 dark:border-blue-700 mb-6">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-info-circle text-blue-600 text-xl mt-1"></i>
                            <div>
                                <p class="font-semibold text-blue-800 dark:text-blue-200 mb-2">الأسعار الحالية:</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-blue-600">التجار:</span>
                                        <span class="font-bold">{{ $priceRate->rate_trade }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600">الفنيين:</span>
                                        <span class="font-bold">{{ $priceRate->rate_technician }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600">العملاء:</span>
                                        <span class="font-bold">{{ $priceRate->rate_client }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Price Rates Section -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-6 rounded-xl border border-green-200 dark:border-green-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="bi bi-tag text-green-600"></i>
                            تحديد نسب التسعير
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="rate_trade" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-shop text-blue-600"></i>
                                    نسبة التجار
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="rate_trade" 
                                        id="rate_trade"
                                        value="{{ old('rate_trade', $priceRate?->rate_trade ?? 100) }}"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <span class="font-bold">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">نسبة السعر للعملاء التجار</p>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="rate_technician" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-tools text-orange-600"></i>
                                    نسبة الفنيين
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="rate_technician" 
                                        id="rate_technician"
                                        value="{{ old('rate_technician', $priceRate?->rate_technician ?? 100) }}"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <span class="font-bold">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">نسبة السعر للفنيين</p>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="rate_client" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-person text-green-600"></i>
                                    نسبة العملاء
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="rate_client" 
                                        id="rate_client"
                                        value="{{ old('rate_client', $priceRate?->rate_client ?? 100) }}"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <span class="font-bold">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">نسبة السعر للعملاء العاديين</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-info-circle"></i>
                            سيتم تطبيق هذه النسب على جميع منتجات الفئة
                        </div>
                        <div class="flex gap-3">
                            <button 
                                type="button" 
                                onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                                <i class="bi bi-x-lg"></i>
                                إلغاء
                            </button>
                            <button 
                                type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                حفظ الأسعار
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
