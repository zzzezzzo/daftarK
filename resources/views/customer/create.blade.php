<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-person-plus text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">إضافة عميل جديد</h1>
                                <p class="text-blue-100 text-sm">أدخل بيانات العميل الجديد</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.index') }}" 
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
                <form action="{{ route('customer.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Customer Type Selection -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-4 items-center gap-2">
                            <i class="bi bi-person-badge text-blue-600"></i>
                            نوع العميل
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="permanent" class="peer sr-only" {{ old('type', 'permanent') == 'permanent' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all hover:border-blue-300">
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-person-check text-2xl text-blue-600"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">عميل دائم</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">لديه محفظة ورصيد</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="walkin" class="peer sr-only" {{ old('type', 'walkin') == 'walkin' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all hover:border-purple-300">
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-person-walking text-2xl text-purple-600"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">عميل عابر</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">بدون محفظة</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Price Type Selection -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-6 rounded-xl border border-green-200 dark:border-green-700">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-4 items-center gap-2">
                            <i class="bi bi-tag text-green-600"></i>
                            نوع التسعير
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="price_type" value="client" class="peer sr-only" {{ old('price_type', 'client') == 'client' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition-all hover:border-green-300">
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-person text-2xl text-green-600"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">سعر العملاء</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">الأسعار العادية</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="price_type" value="trade" class="peer sr-only" {{ old('price_type', 'trade') == 'trade' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all hover:border-blue-300">
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-shop text-2xl text-blue-600"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">سعر التجار</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">أسعار الجملة</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="price_type" value="technical" class="peer sr-only" {{ old('price_type', 'technical') == 'technical' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 transition-all hover:border-orange-300">
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-tools text-2xl text-orange-600"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">سعر الفنيين</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">أسعار الخدمات</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                <i class="bi bi-person text-blue-600"></i>
                                اسم العميل
                            </label>
                            <div class="relative">
                                <input 
                                    placeholder="ادخل اسم العميل الكامل" 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    value="{{ old('name') }}"
                                    class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                    required>
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="phone" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                <i class="bi bi-telephone text-blue-600"></i>
                                رقم الهاتف
                            </label>
                            <div class="relative">
                                <input 
                                    type="tel" 
                                    placeholder="ادخل رقم الهاتف" 
                                    name="phone" 
                                    id="phone" 
                                    value="{{ old('phone') }}"
                                    class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                    required>
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="bi bi-telephone"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-info-circle"></i>
                            جميع الحقول مطلوبة ما عدا المحفظة
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
                                إضافة العميل
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>