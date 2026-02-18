<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-yellow-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-file-earmark-text text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">فواتير الموردين</h1>
                                <p class="text-orange-100 text-sm">إدارة جميع فواتير الموردين</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('suppliers.index') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-arrow-left"></i>
                                <span>الموردين</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('supplier.invoices.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن فاتورة بالرقم أو المورد أو المبلغ..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <select name="filter" class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="">جميع الفواتير</option>
                        <option value="paid" {{ request('filter') == 'paid' ? 'selected' : '' }}>الفواتير المدفوعة</option>
                        <option value="unpaid" {{ request('filter') == 'unpaid' ? 'selected' : '' }}>الفواتير غير المدفوعة</option>
                        <option value="partially_paid" {{ request('filter') == 'partially_paid' ? 'selected' : '' }}>الفواتير المدفوعة جزئياً</option>
                    </select>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-red-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </form>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-list-ul text-orange-600"></i>
                        سجل الفواتير
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">رقم الفاتورة</th>
                                <th class="p-4 text-right font-semibold">المورد</th>
                                <th class="p-4 text-right font-semibold">التاريخ</th>
                                <th class="p-4 text-right font-semibold">المبلغ الإجمالي</th>
                                <th class="p-4 text-right font-semibold">المبلغ المدفوع</th>
                                <th class="p-4 text-right font-semibold">المبلغ المتبقي</th>
                                <th class="p-4 text-right font-semibold">الحالة</th>
                                <th class="p-4 text-center font-semibold">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-orange-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center">
                                                <i class="bi bi-receipt text-orange-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium">{{ $invoice->invoice_number }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                                <i class="bi bi-truck text-red-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium">{{ $invoice->supplier->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-gray-600 dark:text-gray-400">
                                        {{ $invoice->date->format('Y-m-d') }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-800 dark:text-white">
                                        {{ number_format($invoice->total_amount, 2) }} ج.م
                                    </td>
                                    <td class="p-4 text-right font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($invoice->paid_amount, 2) }} ج.م
                                    </td>
                                    <td class="p-4 text-right font-bold text-orange-600 dark:text-orange-400">
                                        {{ number_format($invoice->Remaining_amount, 2) }} ج.م
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            @if($invoice->states == 'paid') bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400
                                            @elseif($invoice->states == 'unpaid') bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400
                                            @elseif($invoice->states == 'partially_paid') bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400
                                            @else bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 @endif">
                                            @if($invoice->states == 'paid') مدفوعة بالكامل
                                            @elseif($invoice->states == 'unpaid') غير مدفوعة
                                            @elseif($invoice->states == 'partially_paid') مدفوعة جزئياً
                                            @else غير محدد @endif
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('accountStatement.show', [$invoice->supplier_id, $invoice->id]) }}" 
                                               class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                               title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('accountStatement.edit', [$invoice->supplier_id, $invoice->id]) }}" 
                                               class="p-2 bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-800/30 transition-colors"
                                               title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button onclick="deleteInvoice({{ $invoice->id }})" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="bi bi-file-earmark-text text-4xl"></i>
                                            <span>لا توجد فواتير حالياً</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteInvoice(id) {
            if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟')) {
                // Placeholder for delete functionality
                alert('Delete functionality will be implemented soon');
            }
        }
    </script>
</x-app-layout>
