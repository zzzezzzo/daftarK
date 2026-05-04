<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-receipt text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">كشف حساب العميل</h1>
                                <p class="text-blue-100 text-sm">{{ $customer->name }}</p>
                            </div>
                            <div class="flex gap-2">
                                
                                <a href="{{ route('customerAccountStatement.create', $customer->id) }}" 
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>إنشاء فاتورة جديدة</span>
                                </a>
                                
                                @if(!$customer->wallet)
                                    <a href="{{ route('customerAccountStatement.index', $customer->id) }}" 
                                       class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                        <i class="bi bi-arrow-clockwise text-blue-600"></i>
                                        <span>العودة إلى آخر تحديث</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <p>آخر تحديث</p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ now()->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Current Balance Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="bi bi-cash-stack text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">الرصيد الحالي</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($remaining_amount, 2) }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">ج.م</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>إجمالي المبلغ المتبقي</span>
                        <i class="bi bi-graph-up text-green-500"></i>
                    </div>
                </div>

                <!-- Wallet Balance Card -->
                @if($customer->wallet && $customer->type === 'permanent')
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="bi bi-wallet2 text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">رصيد المحفظة</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($customer->wallet->balance, 2) }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">ج.م</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>متاح للدفع التلقائي</span>
                        <i class="bi bi-shield-check text-blue-500"></i>
                    </div>
                </div>
                @endif

                <!-- Total Sales Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="bi bi-cart-check text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">إجمالي المبيعات</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($invoices->where('type', 'payment')->sum('total_amount'), 2) }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">ج.م</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $invoices->where('type', 'payment')->count() }} فاتورة</span>
                        <i class="bi bi-trending-up text-purple-500"></i>
                    </div>
                </div>

                <!-- Actions Card -->
                @if ($customer->type == "permanent")
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 border border-indigo-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <i class="bi bi-lightning-charge text-white text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-white/80 uppercase tracking-wide">إجراءات سريعة</p>
                            <p class="text-lg font-bold text-white">{{ $customer->type == 'permanent' ? 'عميل دائم' : 'عميل عابر' }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <button onclick="document.getElementById('walletModal').classList.remove('hidden')" class="w-full bg-white/20 hover:bg-white/30 backdrop-blur text-white px-3 py-2 rounded-lg text-sm transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-plus-circle"></i>
                            شحن المحفظة
                        </button>
                        <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="w-full bg-white/20 hover:bg-white/30 backdrop-blur text-white px-3 py-2 rounded-lg text-sm transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-cash-stack"></i>
                            سحب من الخزينة
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Wallet Modal -->
            <div id="walletModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="bi bi-wallet2"></i>
                                شحن محفظة العميل
                            </h3>
                            <button onclick="document.getElementById('walletModal').classList.add('hidden')" class="text-white/80 hover:text-white">
                                <i class="bi bi-x-lg text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('customerWallet.store', $customer->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المبلغ</label>
                                <div class="relative">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">ج.م</span>
                                    <input type="number" name="balance" step="0.01" min="0.01" required
                                           class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium">
                                <i class="bi bi-check-circle"></i>
                                تأكيد الشحن
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment Modal -->
            <div id="paymentModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="bi bi-cash-stack"></i>
                                سحب من الخزينة
                            </h3>
                            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-white/80 hover:text-white">
                                <i class="bi bi-x-lg text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <form id="paymentForm" method="POST" action="{{ route('customer.treasury.payment', $customer->id) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المبلغ</label>
                                <div class="relative">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">ج.م</span>
                                    <input type="number" name="amount" step="0.01" min="0.01" required
                                           class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف</label>
                                <textarea name="description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                                          placeholder="وصف العملية..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all font-medium">
                                <i class="bi bi-check-circle"></i>
                                تأكيد السحب
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-list-ul text-blue-600"></i>
                            سجل الفواتير
                        </h3>
                    </div>
                    <!-- Search Filter -->
                    <form action="{{ route('customerAccountStatement.index', $customer->id) }}" method="GET" class="mb-4">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}" 
                                    placeholder="ابحث في الفواتير..." 
                                    class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all">
                            </div>
                            <select name="filter" class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">جميع الفواتير</option>
                                <option value="paid" {{ request('filter') == 'paid' ? 'selected' : '' }}>المدفوعة فقط</option>
                                <option value="unpaid" {{ request('filter') == 'unpaid' ? 'selected' : '' }}>غير المدفوعة</option>
                                <option value="partial" {{ request('filter') == 'partial' ? 'selected' : '' }}>المدفوعة جزئياً</option>
                            </select>
                            <button type="submit" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-calendar text-blue-600"></i>
                                        التاريخ
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-receipt text-blue-600"></i>
                                        رقم الفاتورة
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-cash-stack text-blue-600"></i>
                                        المبلغ الكلي
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-cash text-blue-600"></i>
                                        المبلغ المدفوع
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <i class="bi bi-cash text-blue-600"></i>
                                        المبلغ المتبقي
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-gear text-blue-600"></i>
                                        إجراءات
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($invoices as $invoice)
                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4 text-gray-600 dark:text-gray-400 font-medium">{{ $invoice->date }}</td>
                                <td class="p-4">
                                    <a href="{{ route('customerAccountStatement.show', [$customer->id, $invoice->id]) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="p-4 text-right text-gray-800 dark:text-gray-200 font-medium">{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                                <td class="p-4 text-right text-gray-800 dark:text-gray-200 font-medium">{{ number_format($invoice->paid_amount, 2) }} ج.م</td>
                                <td class="p-4 text-right font-bold">
                                    @if($invoice->remining_amount > 0)
                                        <span class="text-green-600 dark:text-green-400">{{ number_format($invoice->remining_amount, 2) }} ج.م</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">{{ number_format($invoice->remining_amount, 2) }} ج.م</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('customerAccountStatement.edit', [$customer->id, $invoice->id]) }}" 
                                           class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                           title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('customerAccountStatement.destroy', [$customer->id, $invoice->id]) }}"
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
                    @if ($invoices->hasPages())
                        <div class="mt-4">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        
    </x-app-layout>