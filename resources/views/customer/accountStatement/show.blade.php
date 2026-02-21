<x-app-layout>
    <div id="print-area" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Screen View - Modern Invoice Header -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden mb-8 print:hidden">
                <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-40 h-40 bg-white/20 rounded-full -translate-x-20 -translate-y-20"></div>
                        <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/20 rounded-full translate-x-16 translate-y-16"></div>
                    </div>
                    
                    <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start gap-6">
                        <!-- Invoice Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="bi bi-receipt-cutoff text-3xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold tracking-tight">فاتورة مبيعات</h1>
                                    <p class="text-indigo-100 text-sm font-medium">SALES INVOICE</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white/10 backdrop-blur p-4 rounded-2xl border border-white/20">
                                    <p class="text-indigo-100 text-xs font-semibold uppercase tracking-wider mb-1">Invoice Number</p>
                                    <p class="text-2xl font-bold">#{{ $invoice->invoice_number }}</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur p-4 rounded-2xl border border-white/20">
                                    <p class="text-indigo-100 text-xs font-semibold uppercase tracking-wider mb-1">Date</p>
                                    <p class="text-2xl font-bold">{{ $invoice->date }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="bg-white/10 backdrop-blur p-6 rounded-2xl border border-white/20 text-right min-w-[280px]">
                            <h2 class="text-xl font-bold mb-3 text-white">{{ $customer->name }}</h2>
                            <div class="space-y-2 text-indigo-100">
                                <p class="flex items-center  gap-2">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span>{{ $customer->phone ?? '—' }}</span>
                                </p>
                                @if($customer->address)
                                <p class="flex items-center  gap-2">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $customer->address }}</span>
                                </p>
                                @endif
                                @if($customer->type)
                                <div class="mt-3 pt-3 border-t border-white/20">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                                        <i class="bi bi-person-badge"></i>
                                        {{ $customer->type == 'permanent' ? 'عميل دائم' : 'عميل عابر' }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print View - Professional Invoice Header -->
            <div class="hidden print:block">
                <div class="border-b-4 border-gray-800 pb-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <!-- Workshop Info -->
                        <div class="text-right">
                            <h1 class="text-2xl font-bold text-gray-800 mb-2">محل اليزيد </h1>
                            <p class="text-sm text-gray-500">مشتول السوق</p>
                            <p class="text-sm text-gray-500">01270042606</p>
                        </div>
                        
                        <!-- Invoice Info -->
                        <div class="text-left">
                            <div class="border-2 border-gray-800 px-4 py-2 rounded">
                                <p class="text-sm text-gray-600">فاتورة رقم</p>
                                <p class="text-xl font-bold">#{{ $invoice->invoice_number }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-sm text-gray-600">التاريخ</p>
                            <p class="font-semibold">{{ $invoice->date }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm text-gray-600">العميل</p>
                            <p class="font-semibold text-lg">{{ $customer->name }}</p>
                            @if($customer->phone)
                            <p class="text-sm text-gray-600">{{ $customer->phone }}</p>
                            @endif
                            @if($customer->address)
                            <p class="text-sm text-gray-600">{{ $customer->address }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 mb-8 print:shadow-none print:rounded-none print:p-0">
                <!-- Products Table -->
                <div class="overflow-x-auto mb-8">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300 print:bg-gray-100">
                            <tr>
                                <th class="p-4 text-right font-semibold border-b-2 border-gray-200 dark:border-gray-600 print:border-b-1 print:border-gray-800">
                                    <div class="flex items-center  gap-2">
                                        <i class="bi bi-hash text-gray-400"></i>
                                        #
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold border-b-2 border-gray-200 dark:border-gray-600 print:border-b-2 print:border-gray-800">
                                    <div class="flex items-center  gap-2">
                                        <i class="bi bi-box text-gray-400"></i>
                                        المنتج
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold border-b-2 border-gray-200 dark:border-gray-600 print:border-b-2 print:border-gray-800">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="bi bi-123 text-gray-400"></i>
                                        الكمية
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold border-b-2 border-gray-200 dark:border-gray-600 print:border-b-2 print:border-gray-800">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="bi bi-currency-dollar text-gray-400"></i>
                                        سعر الوحدة
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold border-b-2 border-gray-200 dark:border-gray-600 print:border-b-2 print:border-gray-800">
                                    <div class="flex items-center  gap-2">
                                        <i class="bi bi-calculator text-gray-400"></i>
                                        الإجمالي
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 print:divide-y print:divide-gray-300">
                            @foreach($invoice->items as $i => $item)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200 print:hover:bg-white">
                                <td class="p-4 text-gray-600 dark:text-gray-400 font-medium border-b border-gray-100 dark:border-gray-700 print:border-b print:border-gray-300">
                                    <span class="print:hidden inline-flex items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full text-sm font-bold">
                                        {{ $i+1 }}
                                    </span>
                                    <span class="hidden print:block font-bold">{{ $i+1 }}</span>
                                </td>
                                <td class="p-4 font-medium text-gray-800 dark:text-gray-200 border-b border-gray-100 dark:border-gray-700 print:border-b print:border-gray-300">
                                    <div class="flex items-start gap-3">
                                        <div class="print:hidden w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-600">{{ $item->product->category->name ?? 'بدون تصنيف' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center border-b border-gray-100 dark:border-gray-700 print:border-b print:border-gray-300">
                                    <span class="print:hidden inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-xl font-bold text-lg min-w-[60px]">
                                        {{ $item->quantity }}
                                    </span>
                                    <span class="hidden print:block font-bold text-lg">{{ $item->quantity }}</span>
                                </td>
                                <td class="p-4 text-center font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 print:border-b print:border-gray-300">
                                    <div class="print:hidden inline-flex items-center gap-2 px-3 py-1 bg-amber-100 text-amber-800 rounded-lg">
                                        <i class="bi bi-currency-dollar"></i>
                                        <span>{{ number_format($item->unit_price, 2) }}</span>
                                    </div>
                                    <span class="hidden print:block">{{ number_format($item->unit_price, 2) }}</span>
                                </td>
                                <td class="p-4 text-right font-bold text-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 border-b-2 border-blue-200 dark:border-blue-800 print:bg-white  print:border-none">
                                    <div class="print:hidden inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl">
                                        <i class="bi bi-cash-stack"></i>
                                        <span>{{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                                    </div>
                                    <span class="hidden print:block text-lg font-bold">{{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:grid-cols-3 print:gap-4">
                    <!-- Total Amount -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl border-2 border-blue-200 dark:border-blue-800 print:bg-white print:border-none print:rounded-lg">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="print:hidden w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="bi bi-receipt text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium print:text-gray-700">الإجمالي الفاتورة</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 print:text-gray-800">{{ number_format($invoice->total_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Amount -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl border-2 border-green-200 dark:border-green-800 print:bg-white print:border-none print:rounded-lg">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="print:hidden w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="bi bi-check-circle text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium print:text-gray-700">المبلغ المدفوع</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400 print:text-gray-800">{{ number_format($invoice->paid_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Remaining Amount -->
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl border-2 border-orange-200 dark:border-orange-800 print:bg-white print:border-none print:rounded-lg">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="print:hidden w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="bi bi-hourglass-split text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium print:text-gray-700">المبلغ المتبقي</p>
                                <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 print:text-gray-800">{{ number_format($invoice->remining_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Footer -->
            <div class="hidden print:block mt-8 pt-6 border-t-2 border-gray-800">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <p>شكراً لتعاملكم مع محل اليزيد</p>
                        <p>Thank you for your business</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>التوقيع:</p>
                        <p class="mt-8 border-b border-gray-400 w-48"></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="no-print flex flex-col lg:flex-row justify-between items-center gap-6 mt-8">
                <a href="{{ route('customerAccountStatement.index', $customer->id) }}"
                   class="group flex items-center gap-3 bg-gray-600 text-white px-8 py-4 rounded-2xl hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-xl">
                    <i class="bi bi-arrow-right text-xl"></i>
                    <span class="font-semibold">رجوع للقائمة</span>
                </a>
                
                <div class="flex gap-4">
                    <button onclick="window.print()"
                        class="group flex items-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-xl">
                        <i class="bi bi-printer-fill text-xl"></i>
                        <span class="font-semibold">طباعة الفاتورة</span>
                    </button>
                    
                    <button onclick="window.open('{{ route('customerAccountStatement.edit', [$customer->id, $invoice->id]) }}', '_blank')"
                        class="group flex items-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-xl">
                        <i class="bi bi-pencil-square text-xl"></i>
                        <span class="font-semibold">تعديل الفاتورة</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
