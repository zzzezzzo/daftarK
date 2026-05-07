<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-truck text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">كشف حساب المورد</h1>
                                <p class="text-orange-100 text-sm">{{ $supplier->name }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openPaymentModal()" 
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-cash-stack"></i>
                                <span>دفع من الخزينة</span>
                            </button>
                            <a href="{{ route('accountStatement.create', $supplier->id) }}" 
                                class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-plus-lg"></i>
                                <span>فاتورة جديدة</span>
                            </a>
                            <a href="{{ route('accountStatement.create', $supplier->id) }}?type=return" 
                                class="bg-red-500/20 hover:bg-red-500/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-arrow-return-left"></i>
                                <span>فاتورة مرتجع</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-down-circle text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المشتريات</p>
                            <p class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($invoices->where('type', 'purchase')->sum('total_amount'), 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-return-left text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المرتجعات</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ number_format($invoices->where('type', 'return')->sum('total_amount'), 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-up-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المدفوع</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($invoices->sum('paid_amount'), 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-exclamation-triangle text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الصافي المستحق</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($invoices->where('type', 'purchase')->sum('total_amount') - $invoices->where('type', 'return')->sum('total_amount') - $invoices->sum('paid_amount'), 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Modal -->
            <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 m-4 max-w-md w-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">دفع من الخزينة</h3>
                        <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form id="paymentForm" method="POST" action="{{ route('supplier.treasury.payment', $supplier->id) }}" >
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="paymentAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مبلغ الدفعة
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="paymentAmount" 
                                        name="amount" 
                                        step="0.01"
                                        min="0.01"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label for="paymentDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الوصف
                                </label>
                                <textarea 
                                    id="paymentDescription" 
                                    name="description" 
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white resize-none"
                                    placeholder="وصف الدفعة"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button 
                                type="button" 
                                onclick="closePaymentModal()"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                إلغاء
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                دفع
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-list-ul text-orange-600"></i>
                            سجل الفواتير
                        </h3>
                    </div>
                    <!-- Search Filter -->
                    <form action="{{ route('accountStatement.index', $supplier->id) }}" method="GET" class="mb-4">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}" 
                                    placeholder="ابحث في الفواتير..." 
                                    class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white transition-all">
                            </div>
                            <select name="filter" class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                <option value="">جميع الفواتير</option>
                                <option value="paid" {{ request('filter') == 'paid' ? 'selected' : '' }}>المدفوعة فقط</option>
                                <option value="unpaid" {{ request('filter') == 'unpaid' ? 'selected' : '' }}>غير المدفوعة</option>
                                <option value="partially_paid" {{ request('filter') == 'partially_paid' ? 'selected' : '' }}>المدفوعة جزئياً</option>
                                <option value="purchase" {{ request('filter') == 'purchase' ? 'selected' : '' }}>فواتير الشراء فقط</option>
                                <option value="return" {{ request('filter') == 'return' ? 'selected' : '' }}>فواتير المرتجعات فقط</option>
                            </select>
                            <button type="submit" class="px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                <i class="bi bi-search"></i>
                                بحث
                            </button>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-calendar text-orange-600"></i>
                                        التاريخ
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-receipt text-orange-600"></i>
                                        رقم الفاتورة
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-tag text-orange-600"></i>
                                        النوع
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-cash-stack text-orange-600"></i>
                                        المبلغ الكلي
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-cash text-orange-600"></i>
                                        المبلغ المدفوع
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-cash text-orange-600"></i>
                                        المبلغ المتبقي
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-info-circle text-orange-600"></i>
                                        الحالة
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-gear text-orange-600"></i>
                                        إجراءات
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-orange-50 dark:hover:bg-gray-700 transition-colors @if($invoice->type == 'return') bg-red-50/50 dark:bg-red-900/10 @endif">
                                    <td class="p-4 text-gray-600 dark:text-gray-400 font-medium">{{ $invoice->date }}</td>
                                    <td class="p-4">
                                        <a href="{{ route('accountStatement.show', [$supplier->id, $invoice->id]) }}" 
                                           class="text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 font-semibold hover:underline">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            @if($invoice->type == 'purchase') bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400
                                            @elseif($invoice->type == 'return') bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400
                                            @else bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 @endif">
                                            @if($invoice->type == 'purchase') شراء
                                            @elseif($invoice->type == 'return') مرتجع
                                            @else {{ $invoice->type }} @endif
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-gray-800 dark:text-gray-200 font-medium">{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                                    <td class="p-4 text-right text-gray-800 dark:text-gray-200 font-medium">{{ number_format($invoice->paid_amount, 2) }} ج.م</td>
                                    <td class="p-4 text-right font-bold">
                                        @if($invoice->Remaining_amount > 0)
                                            <span class="text-red-600 dark:text-red-400">{{ number_format($invoice->Remaining_amount, 2) }} ج.م</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400">{{ number_format($invoice->Remaining_amount, 2) }} ج.م</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            @if($invoice->states == 'paid') bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400
                                            @elseif($invoice->states == 'partially_paid') bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400
                                            @else bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 @endif">
                                            @if($invoice->states == 'paid') مدفوعة بالكامل
                                            @elseif($invoice->states == 'partially_paid') مدفوعة جزئياً
                                            @else غير مدفوعة @endif
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('accountStatement.edit',[$supplier->id, $invoice->id]) }}" 
                                               class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                               title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('accountStatement.destroy', [$supplier->id, $invoice->id]) }}"
                                                  method="POST"
                                                  class="inline"
                                                  data-id="{{ $invoice->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('هل أنت متأكد من حذف الفاتورة: {{ $invoice->invoice_number }}؟')"
                                                       class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                       title="حذف">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
        <script>
            localStorage.removeItem("invoiceProducts");
        </script>
    @endif

    <script>
        function openPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentForm').reset();
        }
        
    </script>
</x-app-layout>