<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-indigo-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-arrow-left-right text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">معاملات العملاء</h1>
                                <p class="text-purple-100 text-sm">إدارة جميع معاملات العملاء</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('customer.index') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-arrow-left"></i>
                                <span>رجوع</span>
                            </a>
                            <a href="{{ route('customerAccountStatement.createTransaction', 0) }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-plus-lg"></i>
                                <span>معاملة جديدة</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-cash-stack text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المبيعات</p>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalSales, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-return-left text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المرتجعات</p>
                            <p class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($totalReturns, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-down-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المدفوعات</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalPayments, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-wallet2 text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الرصيد الحالي</p>
                            <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($balance, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('customer.transactions.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن معاملة بالوصف أو المبلغ..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <select name="filter" class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <option value="">جميع المعاملات</option>
                        <option value="sale" {{ request('filter') == 'sale' ? 'selected' : '' }}>المبيعات فقط</option>
                        <option value="return" {{ request('filter') == 'return' ? 'selected' : '' }}>المرتجعات فقط</option>
                        <option value="payment" {{ request('filter') == 'payment' ? 'selected' : '' }}>المدفوعات فقط</option>
                    </select>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-list-ul text-purple-600"></i>
                        سجل المعاملات
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">العميل</th>
                                <th class="p-4 text-right font-semibold">التاريخ</th>
                                <th class="p-4 text-right font-semibold">النوع</th>
                                <th class="p-4 text-right font-semibold">المبلغ</th>
                                <th class="p-4 text-right font-semibold">الوصف</th>
                                <th class="p-4 text-right font-semibold">طريقة الدفع</th>
                                <th class="p-4 text-center font-semibold">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                                                <i class="bi bi-person text-purple-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium">{{ $transaction->customer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-gray-600 dark:text-gray-400">
                                        {{-- {{ $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : $transaction->created_at->format('Y-m-d H:i') }} --}}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            @if($transaction->type == 'sale') bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400
                                            @elseif($transaction->type == 'return') bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400
                                            @elseif($transaction->type == 'payment') bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400
                                            @elseif($transaction->type == 'deposit') bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400
                                            @else bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 @endif">
                                            @if($transaction->type == 'sale') بيع
                                            @elseif($transaction->type == 'return') مرتجع
                                            @elseif($transaction->type == 'payment') دفعة
                                            @elseif($transaction->type == 'deposit') إيداع
                                            {{-- @else تعديل @endif --}}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-bold">
                                        @if($transaction->type == 'sale')
                                            <span class="text-blue-600 dark:text-blue-400">+{{ number_format($transaction->amount, 2) }} ج.م</span>
                                        @elseif($transaction->type == 'return')
                                            <span class="text-orange-600 dark:text-orange-400">-{{ number_format($transaction->amount, 2) }} ج.م</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400">-{{ number_format($transaction->amount, 2) }} ج.م</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-gray-800 dark:text-gray-200">{{ $transaction->description }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400">
                                            {{ $transaction->method ?? 'cash' }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="editTransaction({{ $transaction->id }})" 
                                                   class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                                   title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button onclick="deleteTransaction({{ $transaction->id }})" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="bi bi-arrow-left-right text-4xl"></i>
                                            <span>لا توجد معاملات حالياً</span>
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
</x-app-layout>
