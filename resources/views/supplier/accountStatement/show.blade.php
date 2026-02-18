<x-app-layout>
<div class="w-full mx-auto p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl" id="invoiceArea">

    <!-- Header -->
    <div class="flex justify-between items-center border-b pb-6 mb-6 ">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">فاتورة مورد</h1>
            <p class="text-sm text-gray-500">رقم الفاتورة: <span class="font-semibold">{{ $invoice->invoice_number }}</span></p>
            <p class="text-sm text-gray-500">التاريخ: {{ $invoice->date }}</p>
        </div>

        <div class="text-left">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $supplier->name }}</h2>
            <p class="text-sm text-gray-500">{{ $supplier->phone ?? '—' }}</p>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 shadow-inner">

        <!-- Products Table -->
        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">المنتج</th>
                    <th class="p-3">الكمية</th>
                    <th class="p-3">سعر الوحدة</th>
                    <th class="p-3">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 text-white">
                @foreach($invoice->items as $i => $item)
                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="p-3">{{ $i+1 }}</td>
                    <td class="p-3 font-medium">{{ $item->product->name }}</td>
                    <td class="p-3">{{ $item->quantity }}</td>
                    <td class="p-3">{{ number_format($item->unit_price,2) }}</td>
                    <td class="p-3 font-semibold text-blue-600">
                        {{ number_format($item->quantity * $item->unit_price,2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="grid grid-cols-3 gap-4 mt-6 text-center">
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
                <p class="text-gray-500 text-sm">الإجمالي</p>
                <p class="text-xl font-bold text-blue-600">{{ number_format($invoice->total_amount,2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
                <p class="text-gray-500 text-sm">المدفوع</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($invoice->paid_amount,2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
                <p class="text-gray-500 text-sm">المتبقي</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($invoice->Remaining_amount,2) }}</p>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="mt-6 text-white bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><strong>حالة الدفع:</strong> {{ $invoice->states }}</div>
                <div><strong>طريقة الدفع:</strong> {{ $invoice->payment->method ?? '—' }}</div>
                <div><strong>تاريخ الدفع:</strong> {{ $invoice->payment->payment_date ?? '—' }}</div>
            </div>
        </div>

    </div>

    <!-- Actions -->
    <div class="flex justify-between mt-8 print:hidden">
        <a href="{{ route('accountStatement.index', $supplier->id) }}"
           class="bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="bi bi-arrow-bar-left"></i> رجوع
        </a>

        <button onclick="window.print()"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="bi bi-printer-fill"></i> طباعة الفاتورة
        </button>
    </div>

</div>
</x-app-layout>

