<x-app-layout>
<div class="w-full bg-white dark:bg-gray-800 p-6 m-5 rounded-lg shadow">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div>
        <h2 class="text-2xl text-gray-200">تعديل فاتورة {{ $invoice->invoice_number }}</h2>
    </div>
    <form action="{{ route('accountStatement.update',  [$supplier->id, $invoice->id]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- بيانات الفاتورة -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">تاريخ الفاتورة</label>
                <input type="date" name="date" value="{{ old('date', $invoice->date) }}" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">نوع الفاتورة</label>
                <select name="type" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="purchase" {{ $invoice->type == 'purchase' ? 'selected' : '' }}>شراء</option>
                    <option value="return" {{ $invoice->type == 'return' ? 'selected' : '' }}>مرتجع</option>
                </select>
            </div>
        </div>

        <!-- تفاصيل المنتجات -->
        <div class="mt-4">
            <h3 class="text-lg text-gray-800 dark:text-gray-200 mb-2">تفاصيل المنتجات</h3>
            <div id="itemsWrapper" class="space-y-3">
                @foreach($invoice->items as $index => $item)
                <div class="flex gap-3 items-end">
                    <div class="w-1/3 relative">
                        <label class="block text-gray-700 dark:text-gray-300 mb-1">المنتج</label>
                        <select name="products[{{ $index }}][product_id]" class="w-full p-2 border rounded">
                            <option value="" disabled>اختر المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 mb-1">الكمية</label>
                        <input type="number" name="products[{{ $index }}][quantity]" value="{{ old("products.$index.quantity", $item->quantity) }}" min="1" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 mb-1">سعر الوحدة</label>
                        <input type="number" name="products[{{ $index }}][unit_price]" value="{{ old("products.$index.unit_price", $item->unit_price) }}" step="0.01" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- hidden لتحديد العنصر -->
                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $item->id }}">

                    <button type="button" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 removeItemBtn">حذف</button>
                </div>
                @endforeach
            </div>
            <button type="button" id="addItemBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">إضافة منتج</button>
        </div>

        <!-- الدفع -->
        <div class="grid grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">المبلغ المدفوع</label>
                <input type="number" name="paid_amount" step="0.01" value="{{ old('paid_amount', $invoice->paid_amount) }}" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">حالة الدفع</label>
                <select name="states" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="paid" {{ $invoice->states == 'paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
                    <option value="partially_paid" {{ $invoice->states == 'partially_paid' ? 'selected' : '' }}>مدفوع جزئي</option>
                    <option value="unpaid" {{ $invoice->states == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع</label>
                <select name="paymentMethod" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cash" {{ $invoice->payment?->method == 'cash' ? 'selected' : '' }}>كاش</option>
                    <option value="bank" {{ $invoice->payment?->method == 'bank' ? 'selected' : '' }}>تحويل بنكي</option>
                </select>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-all">حفظ التعديلات</button>
        </div>

    </form>
</div>
<script>
    const products = @json($products);

</script>


</x-app-layout>
