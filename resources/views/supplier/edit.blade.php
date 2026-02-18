<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-pencil-square text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">تعديل مورد</h1>
                                <p class="text-yellow-100 text-sm">تعديل بيانات المورد: {{ $supplier->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('suppliers.index') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-arrow-right"></i>
                            <span>عودة</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-xl mb-6">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-triangle text-red-500 ml-3 text-xl"></i>
                        <div>
                            <p class="font-medium">يرجى تصحيح الأخطاء التالية:</p>
                            <ul class="mt-2 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('put')
                    
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-info-circle text-yellow-600"></i>
                            المعلومات الأساسية
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    اسم المورد <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        value="{{ $supplier->name }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white transition-all"
                                        placeholder="أدخل اسم المورد">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                        <i class="bi bi-person text-gray-400"></i>
                                    </div>
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    رقم الهاتف <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="tel" 
                                        id="phone" 
                                        name="phone" 
                                        value="{{ $supplier->phone }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white transition-all"
                                        placeholder="أدخل رقم الهاتف">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                        <i class="bi bi-telephone text-gray-400"></i>
                                    </div>
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-6">
                        <a href="{{ route('suppliers.index') }}" 
                           class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center font-medium">
                            <i class="bi bi-arrow-right ml-2"></i>
                            إلغاء
                        </a>
                        <button 
                            type="submit" 
                            class="flex-1 bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-3 rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all font-medium">
                            <i class="bi bi-check-circle ml-2"></i>
                            تعديل المورد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>