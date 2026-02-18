<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-yellow-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-cash-stack text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">إضافة معاملة للمورد</h1>
                                <p class="text-orange-100 text-sm">{{ $supplier->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('supplierAccountStatement.transactionIndex', $supplier->id) }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i>
                            <span>رجوع</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transaction Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <form action="{{ route('supplierAccountStatement.storeTransaction', $supplier->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Transaction Type Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="bi bi-tag text-orange-600"></i>
                                نوع المعاملة
                            </label>
                            <select 
                                id="type" 
                                name="type" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all">
                                <option value="" disabled selected>اختر نوع المعاملة</option>
                                <option value="deposit">إيداع</option>
                                <option value="withdrawal">سحب</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="bi bi-cash text-orange-600"></i>
                                المبلغ
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    id="amount" 
                                    name="amount" 
                                    step="0.01"
                                    min="0.01"
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all"
                                    placeholder="0.00">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">ج.م</span>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="bi bi-calendar text-orange-600"></i>
                            تاريخ المعاملة
                        </label>
                        <input 
                            type="datetime-local" 
                            id="transaction_date" 
                            name="transaction_date" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="bi bi-text-paragraph text-orange-600"></i>
                            الوصف
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all resize-none"
                            placeholder="اكتب وصفاً للمعاملة..."></textarea>
                    </div>

                    <!-- Supplier Info Display -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                <i class="bi bi-truck text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">المورد</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $supplier->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $supplier->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('supplierAccountStatement.transactionIndex', $supplier->id) }}" 
                           class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <i class="bi bi-x-lg ml-2"></i>
                            إلغاء
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-red-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                            <i class="bi bi-check-lg"></i>
                            حفظ المعاملة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set current datetime as default
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('transaction_date');
            const now = new Date();
            const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
                .toISOString()
                .slice(0, 16);
            dateInput.value = localDateTime;
        });
    </script>
</x-app-layout>
