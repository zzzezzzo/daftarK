<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r {{ $cashBox->status === 'active' ? 'from-green-600 to-teal-600' : 'from-gray-600 to-gray-700' }} p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-cash-stack text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">{{ $cashBox->name }}</h1>
                                <p class="text-green-100 text-sm">{{ $cashBox->description ?? 'صندوق نقدي' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('cashBoxes.report', $cashBox->id) }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>تقرير</span>
                            </a>
                            <a href="{{ route('cashBoxes.index') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span>عودة</span>
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
                            <i class="bi bi-wallet2 text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الرصيد الافتتاحي</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($cashBox->opening_balance, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-down-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الداخل</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($cashBox->total_in, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-arrow-up-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الخارج</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ number_format($cashBox->total_out, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                            <i class="bi bi-cash text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الرصيد الحالي</p>
                            <p class="text-2xl font-bold {{ $cashBox->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $cashBox->balance_formatted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="bi bi-plus-circle text-blue-600"></i>
                    إضافة معاملة جديدة
                </h3>
                
                <form action="{{ route('cashBoxes.addTransaction', $cashBox->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            نوع المعاملة <span class="text-red-500">*</span>
                        </label>
                        <select name="type" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">اختر النوع</option>
                            <option value="in">إيداع (داخل)</option>
                            <option value="out">سحب (خارج)</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            المبلغ <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="amount" 
                            step="0.01"
                            min="0.01"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="0.00">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الوصف <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="description" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="أدخل وصف المعاملة">
                    </div>

                    <!-- Submit Button -->
                    <div class="md:col-span-2">
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all font-medium">
                            <i class="bi bi-plus-circle ml-2"></i>
                            إضافة المعاملة
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-list-ul text-blue-600"></i>
                        سجل المعاملات
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                                <tr>
                                    <th class="p-4 text-right font-semibold">التاريخ والوقت</th>
                                    <th class="p-4 text-right font-semibold">النوع</th>
                                    <th class="p-4 text-right font-semibold">المبلغ</th>
                                    <th class="p-4 text-right font-semibold">الوصف</th>
                                    <th class="p-4 text-center font-semibold">المستخدم</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="p-4 text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="p-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $transaction->type === 'in' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                                {{ $transaction->type === 'in' ? 'إيداع' : 'سحب' }}
                                            </span>
                                        </td>
                                        <td class="p-4 font-bold {{ $transaction->type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $transaction->amount_formatted }}
                                        </td>
                                        <td class="p-4 text-gray-800 dark:text-gray-200">{{ $transaction->description }}</td>
                                        <td class="p-4 text-gray-600 dark:text-gray-400">{{ $transaction->user->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="mt-6 flex justify-center">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
