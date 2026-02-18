<x-app-layout>
    
    <div class="w-full px-2">
        @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        <div class="flex justify-between items-center m-5">
            <h2 class="text-2xl text-gray-100 my-5">إنشاء فاتورة مورد جديدةلـ <span class="text-green-500">{{ $supplier->name }}</span></h2>
            <a href="{{ route('accountStatement.index', $supplier->id) }}" class="inline-block bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-all mt-5">قائمة الفواتير</a>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <form action="{{ route('accountStatement.store', $supplier->id) }}" method="POST" class="space-y-6">
                @csrf
                <!-- بيانات الفاتورة -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">تاريخ الفاتورة</label>
                        <input type="date" name="date" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">نوع الفاتورة</label>
                        <select name="type" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="purchase">شراء</option>
                            <option value="return">مرتجع</option>
                        </select>
                    </div>
                </div>

                <!-- تفاصيل المنتجات -->
                <div class="mt-4">
                    <h3 class="text-lg text-gray-800 dark:text-gray-200 mb-2">تفاصيل المنتجات</h3>
                    <div id="itemsWrapper" class="space-y-3">
                        <div class="flex gap-3 items-end">
                            <div class="w-1/3 relative">
                                <label class="block text-gray-700 dark:text-gray-300 mb-1">المنتج</label>

                                <!-- Select للمنتجات الموجودة -->
                                <select name="products[0][product_id]" id="productSelect" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateStockDisplay(this)">
                                    <option value="" disabled selected>اختر المنتج</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">{{ $product->name }} (المتوفر: {{ $product->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-1/3">
                                <label class="block text-gray-700 dark:text-gray-300 mb-1">الكمية</label>
                                <input type="number" name="products[0][quantity]" value="1" min="1" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="validateSupplierStock(this)">
                                <div id="supplierStockWarning-0" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <span class="stock-warning-text"></span>
                                </div>
                            </div>
                            <div class="w-1/3">
                                <label class="block text-gray-700 dark:text-gray-300 mb-1">سعر الوحدة</label>
                                <input type="number" name="products[0][unit_price]" step="0.01" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="button" onclick="this.closest('.flex').remove()" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">حذف</button>
                        </div>
                    </div>
                    <button type="button" id="addItemBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">إضافة منتج</button>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">المبلغ المدفوع</label>
                        <input type="number" name="paid_amount" step="0.01" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">حالة الدفع</label>
                        <select name="states" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="paid">مدفوع بالكامل</option>
                            <option value="partially_paid">مدفوع جزئي</option>
                            <option value="unpaid">غير مدفوع</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع</label>
                        <select name="paymentMethod" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="cash">كاش</option>
                            <option value="bank">تحويل بنكي</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-all">إنشاء الفاتورة</button>
                </div>

            </form>
        </div>
    </div>

    <!-- JS لإضافة منتجات ديناميكي -->
 <script>
    // products من قاعدة البيانات
    const productsList = @json($products);
    let itemIndex = 1;
</script>


</x-app-layout>
