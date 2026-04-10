<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-box text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">المنتجات</h1>
                                <p class="text-blue-100 text-sm">إدارة المنتجات والأسعار</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('products.labels.print') }}"
                               target="_blank"
                               rel="noopener"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-printer"></i>
                                <span>طباعة باركود الكل</span>
                            </a>
                            <a href="{{ route('products.create') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                <i class="bi bi-plus-lg"></i>
                                <span>منتج جديد</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Out of Stock Alert -->
            @if ($outofstockProduct->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl mb-6 flex items-start gap-3">
                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-exclamation-triangle text-white text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold mb-2">منتجات غير متوفرة في المخزن:</p>
                    <div class="space-y-1">
                        @foreach($outofstockProduct as $product)
                            <li class="flex items-start gap-2">
                                <i class="bi bi-dot text-red-500"></i>
                                <span>{{ $product->name }}</span>
                            </li>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن منتج بالاسم أو الكود..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-upc text-blue-600"></i>
                                        الكود
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-box text-blue-600"></i>
                                        اسم المنتج
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-tag text-blue-600"></i>
                                        الفئة
                                    </div>
                                </th>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-currency-dollar text-blue-600"></i>
                                        السعر الأساسي
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-shop text-blue-600"></i>
                                        سعر التجار
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-tools text-blue-600"></i>
                                        سعر الفنيين
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-person text-blue-600"></i>
                                        سعر العملاء
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-box text-blue-600"></i>
                                        الكمية
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
                        <tbody>
                            @foreach($products as $product)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $product->code }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-white">{{ $product->name }}</p>
                                            @if($product->category)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $product->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($product->category)
                                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-full text-sm font-medium">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">
                                            بدون فئة
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-center">
                                        <span class="font-bold text-gray-800 dark:text-white">{{ number_format($product->price_base, 2) }} ج.م</span>
                                        @if($product->category && $product->category->priceRate)
                                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                <i class="bi bi-percent"></i> أسعار ديناميكية
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                        {{ $product->price_trade }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">
                                        {{ $product->price_technician }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                        {{ $product->price_customer }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="text-center">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            {{ $product->stock < 150 ? 'bg-red-100 text-red-800' 
                                                : ($product->stock < 50 ? 'bg-yellow-100 text-yellow-800' 
                                                : 'bg-green-100 text-green-800') }}">
                                            {{ $product->stock }}
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            قطعة
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products.label.print', $product) }}"
                                           target="_blank"
                                           rel="noopener"
                                           class="p-2 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-800/30 transition-colors"
                                           title="باركود">
                                            <i class="bi bi-upc-scan"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product->id) }}" 
                                           class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                           title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}"
                                              method="POST"
                                              class="inline"
                                              data-id="{{ $product->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف"
                                                   onclick="if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) this.form.submit()">
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
                
                <!-- Pagination -->
                @if($products->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
