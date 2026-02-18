<x-app-layout>
    <div id="print-area" class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4 ">
        <div class="max-w-6xl mx-auto">
            <!-- Modern Invoice Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                    <div class="flex flex-col lg:flex-row justify-between items-start gap-4 head-invoice">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                    <i class="bi bi-receipt text-2xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold">فاتورة مبيعات</h1>
                                    <p class="text-blue-100 text-sm">Sales Invoice</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white/10 p-3 rounded-lg backdrop-blur">
                                    <p class="text-blue-100 text-xs">رقم الفاتورة</p>
                                    <p class="text-xl font-bold">#{{ $invoice->invoice_number }}</p>
                                </div>
                                <div class="bg-white/10 p-3 rounded-lg backdrop-blur">
                                    <p class="text-blue-100 text-xs">التاريخ</p>
                                    <p class="text-xl font-bold">{{ $invoice->date }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur text-right user-info">
                            <h2 class="text-xl font-bold mb-2">{{ $customer->name }}</h2>
                            <p class="text-blue-100 text-sm">
                                <i class="bi bi-telephone-fill ml-2"></i>{{ $customer->phone ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <!-- Products Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">#</th>
                                <th class="p-4 text-right font-semibold">المنتج</th>
                                <th class="p-4 text-center font-semibold">الكمية</th>
                                <th class="p-4 text-center font-semibold">سعر الوحدة</th>
                                <th class="p-4 text-right font-semibold">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($invoice->items as $i => $item)
                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4 text-gray-600 dark:text-gray-400 font-medium">{{ $i+1 }}</td>
                                <td class="p-4 font-medium text-gray-800 dark:text-gray-200">{{ $item->product->name }}</td>
                                <td class="p-4 text-center">
                                    <span class="bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="p-4 text-right font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($item->quantity * $item->unit_price, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Card -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                <i class="bi bi-receipt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-1">الإجمالي الفاتورة</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($invoice->total_amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-right no-print">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <p>المبلغ المدفوع</p>
                                <p class="text-lg font-semibold">{{ number_format($invoice->paid_amount, 2) }}</p>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <p>المبلغ المتبقي</p>
                                <p class="text-lg font-semibold">{{ number_format($invoice->remaining_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="no-print">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8">
                    <a href="{{ route('customerAccountStatement.index', $customer->id) }}"
                       class="flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all transform hover:scale-105 shadow-lg">
                        <i class="bi bi-arrow-right text-lg"></i>
                        <span>رجوع للقائمة</span>
                    </a>
                    
                    <div class="flex gap-3">
                        <button onclick="window.print()"
                            class="flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg">
                            <i class="bi bi-printer-fill text-lg"></i>
                            <span>طباعة الفاتورة</span>
                        </button>
                        
                        <button onclick="window.open('{{ route('customerAccountStatement.edit', [$customer->id, $invoice->id]) }}', '_blank')"
                            class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-all transform hover:scale-105 shadow-lg">
                            <i class="bi bi-pencil-square text-lg"></i>
                            <span>تعديل الفاتورة</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
