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

            <!-- Balance Card -->
            @if ($customer->type == 'permanent')
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                                <i class="bi bi-cash-stack text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">الرصيد الحالي</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">إجمالي المبلغ المتبقي</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $remaining_amount }}</p>
                            </div>
                                @if($customer->type == 'permanent')
                                <div class="w-1/2 mt-9">
                                    <!-- Button trigger modal -->
                                    <button id="openModalBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                    المحفظة
                                    </button>
                                    <div id="myModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-11/12 max-w-md">
                                        <!-- Header -->
                                        <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">اضافة رصيد للمحفظة</h2>
                                        <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&times;</button>
                                        </div>

                                        <!-- Body -->
                                        <div class="p-4 text-gray-700 dark:text-gray-300">
                                            <form action="{{ route('customerWallet.store', $customer->id) }}" method="POST">
                                                @csrf
                                                <input type="number" name="balance" id="balance" placeholder="أدخل المبلغ" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                    إضافة
                                                </button>
                                            </form>
                                        </div>
                                        <!-- Footer -->
                                        <div class="flex justify-end gap-2 p-4 border-t border-gray-200 dark:border-gray-700">
                                        <button id="closeModalBtn2" class="bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                                            Close
                                        </button>
                                        {{-- <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                            Save changes
                                        </button> --}}
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endif
                            
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <p>آخر تحديث</p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ now()->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Wallet Balance Display -->
                    @if($customer->wallet )
                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                        <i class="bi bi-wallet2 text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">رصيد المحفظة</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">متاح للدفع التلقائي</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($customer->wallet->balance, 2) }} ج.م</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
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
                </div>
            </div>
        </div>
        
        
    </x-app-layout>