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
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </select>
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
                        
                        <div id="itemsWrapper" class="space-y-4">
                            <!-- First Item (Template) -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="w-full relative">
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 items-center gap-2">
                                        <i class="bi bi-tag text-blue-600"></i>
                                        المنتج
                                    </label>
                                    <div class="relative">
                                        <select 
                                            name="products[0][product_id]" 
                                            id="productSelect"
                                            class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                            required
                                            onchange="updateProductPrice(this)">
                                            <option value="" disabled selected>اختر المنتج</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-category="{{ $product->category_id }}"
                                                        data-base-price="{{ $product->price_base }}"
                                                        data-trade-price="{{ $product->getPriceForCustomerType('trade') }}"
                                                        data-technical-price="{{ $product->getPriceForCustomerType('technical') }}"
                                                        data-client-price="{{ $product->getPriceForCustomerType('client') }}"
                                                        data-stock="{{ $product->stock }}">
                                                    {{ $product->name }} (المتوفر: {{ $product->stock }})
                                                </option>
                                            @endforeach
                                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                                <i class="bi bi-chevron-down"></i>
                                            </div>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full relative">
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 items-center gap-2">
                                        <i class="bi bi-123 text-blue-600"></i>
                                        الكمية
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="number" 
                                            name="products[0][quantity]" 
                                            value="1" 
                                            min="1" 
                                            class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                            required
                                            onchange="calculateItemTotal(this)"
                                            oninput="validateStock(this)">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="bi bi-123"></i>
                                        </div>
                                    </div>
                                    <div id="stockWarning-0" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span class="stock-warning-text"></span>
                                    </div>
                                </div>
                                <div class="w-full flex items-end">
                                    <button 
                                        type="button" 
                                        class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all"
                                        onclick="removeItem(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Total Calculation Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                    <i class="bi bi-calculator text-blue-600"></i>
                                    إجمالي الفاتورة
                                </h3>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    يتم حساب الإجمالي تلقائياً
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-xl text-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">الإجمالي الكلي</div>
                                    <div id="totalAmount" class="text-4xl font-bold text-blue-600 dark:text-blue-400">0.00 ج.م</div>
                                </div>
                            </div>
                        </div>

                    <!-- Payment Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="paid_amount" class="block text-gray-700 dark:text-gray-300 font-semibold  items-center gap-2">
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
                                <label for="states" class="block text-gray-700 dark:text-gray-300 font-semibold  items-center gap-2">
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
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="paymentMethod" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
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
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </select>
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
                                type="button" 
                                id="addItemBtnCreate"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2"
                                onclick="addSingleItem()">
                                <i class="bi bi-plus-lg"></i>
                                <span>إضافة منتج</span>
                            </button>
                            <button 
                                type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                إنشاء الفاتورة
                            </button>
                        </div>
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
    <script src="{{ asset('js/customer-invoice.js') }}"></script>
</x-app-layout>
