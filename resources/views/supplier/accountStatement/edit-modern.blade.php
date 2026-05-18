<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-pencil-square text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">تعديل الفاتورة</h1>
                                <p class="text-blue-100 text-sm">فاتورة رقم: {{ $invoice->invoice_number }} | المورد: {{ $supplier->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('accountStatement.index', $supplier->id) }}" 
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

            <!-- Supplier Info Card -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <i class="bi bi-building text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $supplier->name }}</h3>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                كود: {{ $supplier->code }}
                            </span>
                            @if($supplier->phone)
                                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-full text-sm font-medium">
                                    {{ $supplier->phone }}
                                </span>
                            @endif
                            @if($supplier->email)
                                <span class="px-3 py-1 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-full text-sm font-medium">
                                    {{ $supplier->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form action="{{ route('accountStatement.update', [$supplier->id, $invoice->id]) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Invoice Info Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="date" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-calendar text-blue-600"></i>
                                    تاريخ الفاتورة
                                </label>
                                <div class="relative">
                                    <input 
                                        type="date" 
                                        name="date" 
                                        value="{{ old('date') ?? $invoice->date }}"
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
                                        <option value="purchase" {{ $invoice->type == 'purchase' ? 'selected' : '' }}>شراء</option>
                                        <option value="return" {{ $invoice->type == 'return' ? 'selected' : '' }}>مرتجع</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="states" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-flag text-blue-600"></i>
                                    حالة الفاتورة
                                </label>
                                <div class="relative">
                                    <select 
                                        name="states" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="unpaid" {{ $invoice->states == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                                        <option value="paid" {{ $invoice->states == 'paid' ? 'selected' : '' }}>مدفوعة بالكامل</option>
                                        <option value="partially_paid" {{ $invoice->states == 'partially_paid' ? 'selected' : '' }}>مدفوعة جزئياً</option>
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
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-box text-blue-700"></i>
                                </div>
                                تفاصيل المنتجات
                            </h3>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                سيتم احتساب الأسعار تلقائياً حسب سعر الشراء
                            </div>
                        </div>
                        
                        <!-- Simple Product Addition -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                                        <i class="bi bi-plus-circle text-green-700 text-xl"></i>
                                    </div>
                                    إضافة منتج
                                </h3>
                                <div class="text-sm text-gray-600 dark:text-gray-300">
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
                                        سعر الشراء
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
                                            id="addProductBtn"
                                            onclick="addProductToInvoice()"
                                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                                        <i class="bi bi-plus-circle text-xl"></i>
                                        <span>إضافة للفاتورة</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                        <i class="bi bi-list-ul text-blue-600"></i>
                                        المنتجات المضافة
                                        <span id="productsCount" class="bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-2 py-1 rounded-full text-xs font-bold">0</span>
                                    </h4>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <tr>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <i class="bi bi-box ml-1"></i>اسم المنتج
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <i class="bi bi-currency-dollar ml-1"></i>سعر الشراء
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <i class="bi bi-123 ml-1"></i>الكمية
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <i class="bi bi-calculator ml-1"></i>الإجمالي
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <i class="bi bi-trash ml-1"></i>حذف
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <!-- Products will be added here dynamically -->
                                    </tbody>
                                </table>
                                
                                <div id="emptyState" class="text-center py-12">
                                    <div class="text-gray-400 dark:text-gray-500">
                                        <i class="bi bi-inbox text-6xl mb-4"></i>
                                        <p class="text-lg">لا توجد منتجات مضافة</p>
                                        <p class="text-sm">ابحث عن المنتجات وأضفها للفاتورة</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Inputs for Products -->
                    <div id="productsInputs"></div>

                    <!-- Payment Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-credit-card text-blue-700"></i>
                                </div>
                                بيانات الدفع
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="paid_amount" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-cash text-blue-600"></i>
                                    المبلغ المدفوع
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01"
                                        name="paid_amount" 
                                        value="{{ old('paid_amount', $invoice->paid_amount) }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        placeholder="0.00">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="payment_method" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-credit-card text-blue-600"></i>
                                    طريقة الدفع
                                </label>
                                <div class="relative">
                                    <select 
                                        name="payment_method" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none">
                                        <option value="cash" {{ $invoice->payment_method == 'cash' ? 'selected' : '' }}>نقدي</option>
                                        <option value="bank" {{ $invoice->payment_method == 'bank' ? 'selected' : '' }}>تحويل بنكي</option>
                                        <option value="check" {{ $invoice->payment_method == 'check' ? 'selected' : '' }}>شيك</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="states" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-flag text-blue-600"></i>
                                    حالة الفاتورة
                                </label>
                                <div class="relative">
                                    <select 
                                        name="states" 
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
                                        required>
                                        <option value="unpaid">غير مدفوعة</option>
                                        <option value="paid">مدفوعة بالكامل</option>
                                        <option value="partially_paid">مدفوعة جزئياً</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold mb-1">المجموع الإجمالي</h3>
                                <p class="text-blue-100 text-sm">مجموع قيمة الفاتورة</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">
                                    <span id="totalAmount">{{ $invoice->total_amount }}</span> ج.م
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-3">
                            <i class="bi bi-check-circle text-xl"></i>
                            <span>تحديث الفاتورة</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP variables to JavaScript
        window.products = @json($products);
        window.invoiceItems = @json($invoice->items);
    </script>
    <script src="{{ asset('js/supplier-invoice-edit.js') }}"></script>
</x-app-layout>
