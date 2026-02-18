<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 p-6 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                            <i class="bi bi-plus-circle text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">إنشاء صندوق نقدي جديد</h1>
                            <p class="text-green-100 text-sm">إضافة صندوق نقدي جديد للنظام</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <form action="{{ route('cashBoxes.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            اسم الصندوق <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all"
                                placeholder="أدخل اسم الصندوق">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-tag text-gray-400"></i>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الوصف
                        </label>
                        <div class="relative">
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all resize-none"
                                placeholder="أدخل وصف الصندوق (اختياري)"></textarea>
                            <div class="absolute right-4 top-4">
                                <i class="bi bi-text-paragraph text-gray-400"></i>
                            </div>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opening Balance -->
                    <div>
                        <label for="opening_balance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الرصيد الافتتاحي <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="opening_balance" 
                                name="opening_balance" 
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white transition-all"
                                placeholder="0.00">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-cash text-gray-400"></i>
                            </div>
                        </div>
                        @error('opening_balance')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('cashBoxes.index') }}" 
                           class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center font-medium">
                            <i class="bi bi-arrow-right ml-2"></i>
                            إلغاء
                        </a>
                        <button 
                            type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded-xl hover:from-green-700 hover:to-teal-700 transition-all font-medium">
                            <i class="bi bi-check-circle ml-2"></i>
                            إنشاء الصندوق
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
