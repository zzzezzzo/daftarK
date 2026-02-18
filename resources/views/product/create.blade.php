<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-plus-lg text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">إضافة منتج جديد</h1>
                                <p class="text-blue-100 text-sm">أدخل بيانات المنتج الجديد</p>
                            </div>
                        </div>
                        <a href="{{ route('products.index') }}" 
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
                <form action="{{ route('products.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="bi bi-box text-blue-600"></i>
                            المعلومات الأساسية
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="code" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-upc text-blue-600"></i>
                                    كود المنتج
                                </label>
                                <div class="relative">
                                    <input 
                                        placeholder="مثال: PRD-001" 
                                        type="text" 
                                        name="code" 
                                        id="code" 
                                        value="{{ old('code') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-upc"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-box text-blue-600"></i>
                                    اسم المنتج
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        placeholder="ادخل اسم المنتج الكامل" 
                                        name="name" 
                                        id="name" 
                                        value="{{ old('name') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-6 rounded-xl border border-purple-200 dark:border-purple-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="bi bi-tag text-purple-600"></i>
                            الفئة والأسعار
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="category_id" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-tag text-purple-600"></i>
                                    الفئة
                                </label>
                                <div class="relative">
                                    <select 
                                        name="category_id" 
                                        id="category_id" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="" disabled selected>اختر الفئة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    id="addCategoryBtn"
                                    class="mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all flex items-center gap-2">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>إضافة فئة جديدة</span>
                                </button>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="price_base" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-currency-dollar text-purple-600"></i>
                                    السعر الأساسي
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        name="price_base" 
                                        id="price_base"
                                        value="{{ old('price_base') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="bi bi-info-circle"></i>
                                    سيتم استخدام هذا السعر كأساس لحساب أسعار العملاء المختلفة
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alternative Prices -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-6 rounded-xl border border-green-200 dark:border-green-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="bi bi-percent text-green-600"></i>
                            الأسعار البديلة (اختياري)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="price_trade" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-shop text-blue-600"></i>
                                    سعر التجار
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        name="price_trade" 
                                        id="price_trade"
                                        value="{{ old('price_trade') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="bi bi-info-circle"></i>
                                    سعر خاص بالعملاء التجار
                                </p>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="price_technician" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-tools text-orange-600"></i>
                                    سعر الفنيين
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        name="price_technician" 
                                        id="price_technician"
                                        value="{{ old('price_technician') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="bi bi-info-circle"></i>
                                    سعر خاص بالفنيين
                                </p>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="price_customer" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-person text-green-600"></i>
                                    سعر العملاء
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        name="price_customer" 
                                        id="price_customer"
                                        value="{{ old('price_customer') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-person"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="bi bi-info-circle"></i>
                                    سعر خاص بالعملاء العاديين
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-info-circle"></i>
                            السعر الأساسي مطلوب، والأسعار البديلة اختيارية
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
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                إضافة المنتج
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const url = "{{ route('categories.store') }}";        
        window.categoryConfig = {
            url: url,
            csrf : "{{ csrf_token() }}"
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>
