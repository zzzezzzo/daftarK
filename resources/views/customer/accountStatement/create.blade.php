<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-receipt text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">إنشاء فاتورة جديدة</h1>
                                <p class="text-blue-100 text-sm">للعميل: {{ $customer->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('customerAccountStatement.index', $customer->id) }}" 
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

            <!-- Customer Info Card -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <i class="bi bi-person text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h3>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                {{ $customer->type == 'permanent' ? 'دائم' : 'عابر' }}
                            </span>
                            <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-full text-sm font-medium">
                                {{ $customer->price_type }}
                            </span>
                            @if($customer->wallet)
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                    رصيد المحفظة: {{ number_format($customer->wallet->balance, 2) }} ج.م
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form action="{{ route('customerAccountStatement.store', $customer->id) }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Invoice Info Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="date" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-calendar text-blue-600"></i>
                                    تاريخ الفاتورة
                                </label>
                                <div class="relative">
                                    <input 
                                        type="date" 
                                        name="date" 
                                        value="{{ old('date') ?? now()->format('Y-m-d') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="type" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-tag text-blue-600"></i>
                                    نوع الفاتورة
                                </label>
                                <div class="relative">
                                    <select 
                                        name="type" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="payment">بيع</option>
                                        <option value="return">مرتجع</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-box text-blue-600"></i>
                                تفاصيل المنتجات
                            </h3>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                سيتم احتساب الأسعار تلقائياً حسب نوع العميل والفئة
                            </div>
                        </div>
                        
                        <!-- Simple Product Addition -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                                        <i class="bi bi-plus-circle text-green-600 text-xl"></i>
                                    </div>
                                    إضافة منتج
                                </h3>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    ابحث عن المنتج وأضفه للفاتورة
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                                <!-- Product Name -->
                                <div class="relative">
                                    <label class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                        <i class="bi bi-search text-blue-600"></i>
                                        اسم المنتج
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               id="productSearch"
                                               class="w-full p-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all"
                                               placeholder="ابحث عن منتج..."
                                               autocomplete="off">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="bi bi-search"></i>
                                        </div>
                                    </div>

                                    <!-- Suggestions Box -->
                                    <div id="suggestionsBox"
                                         class="absolute top-full left-0 right-0 mt-2 w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-xl shadow-2xl hidden z-50 max-h-80 overflow-y-auto">
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                        <i class="bi bi-123 text-blue-600"></i>
                                        الكمية
                                    </label>
                                    <div class="relative">
                                        <input type="number"
                                               id="productQty"
                                               class="w-full p-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all"
                                               value="1"
                                               min="1">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="bi bi-123"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price (Readonly) -->
                                <div>
                                    <label class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                        <i class="bi bi-currency-dollar text-blue-600"></i>
                                        السعر
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               id="productPrice"
                                               class="w-full p-4 pr-12 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold"
                                               readonly
                                               placeholder="0.00">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="bi bi-currency-dollar"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Button -->
                                <div>
                                    <label class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                        &nbsp;
                                    </label>
                                    <button type="button"
                                            class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all transform hover:scale-105 shadow-lg font-bold flex items-center justify-center gap-2"
                                            onclick="addProductToInvoice()">
                                        <i class="bi bi-plus-lg text-xl"></i>
                                        إضافة للفاتورة
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Products List -->
                        <div id="productsList" class="mt-6">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                            <i class="bi bi-list-ul text-blue-700"></i>
                                        </div>
                                        المنتجات المضافة
                                    </h4>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span id="productsCount">0</span> منتجات
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-200 dark:border-gray-600">
                                                <th class="p-4 text-right border border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">
                                                    <i class="bi bi-box ml-2"></i>
                                                    المنتج
                                                </th>
                                                <th class="p-4 text-right border border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">
                                                    <i class="bi bi-currency-dollar ml-2"></i>
                                                    السعر
                                                </th>
                                                <th class="p-4 text-right border border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">
                                                    <i class="bi bi-123 ml-2"></i>
                                                    الكمية
                                                </th>
                                                <th class="p-4 text-right border border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">
                                                    <i class="bi bi-calculator ml-2"></i>
                                                    الإجمالي
                                                </th>
                                                <th class="p-4 text-center border border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">
                                                    <i class="bi bi-gear ml-2"></i>
                                                    إجراء
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsTable">
                                            <!-- Products will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            

                            <!-- Total Section -->
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl shadow-lg p-6 mt-6 border border-blue-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                            <i class="bi bi-calculator text-blue-700 text-xl"></i>
                                        </div>
                                        إجمالي الفاتورة
                                    </h3>
                                    <div class="text-3xl font-bold text-blue-700 dark:text-blue-400 flex items-center gap-2">
                                        <span id="totalAmount">0.00 ج.م</span>
                                        <i class="bi bi-currency-dollar text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="paid_amount" class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-cash text-blue-600"></i>
                                    المبلغ المدفوع
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="paid_amount" 
                                        step="0.01" 
                                        value="{{ old('paid_amount') }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        placeholder="0.00">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="space-y-2">
                                <label for="states" class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-check-circle text-blue-600"></i>
                                    حالة الدفع
                                </label>
                                <div class="relative">
                                    <select 
                                        name="states" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="paid">مدفوع بالكامل</option>
                                        <option value="partial">مدفوع جزئي</option>
                                        <option value="unpaid">غير مدفوع</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="space-y-2">
                                <label for="paymentMethod" class="flex items-center gap-2 mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-credit-card text-blue-600"></i>
                                    طريقة الدفع
                                </label>
                                <div class="relative">
                                    <select 
                                        name="paymentMethod" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="cash">كاش</option>
                                        <option value="bank">تحويل بنكي</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-info-circle"></i>
                            جميع الحقول مطلوبة ما عدا المنتجات
                        </div>
                        <div class="flex gap-3">
                            <button 
                                type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                <span>إنشاء الفاتورة</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Hidden input for storing products data -->
                    <div id="productsInputs">
                        <!-- Products will be added here as hidden inputs -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- External JavaScript -->
    <script>
        // Pass PHP variables to JavaScript
        window.products = @json($products);
        window.customerType = '{{ $customer->price_type }}';
    </script>
    <script src="{{ asset('js/customer-invoice-new.js') }}"></script>
</x-app-layout>
