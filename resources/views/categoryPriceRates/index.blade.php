<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-percent text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">أسعار الفئات</h1>
                                <p class="text-green-100 text-sm">إدارة نسب التسعير للفئات المختلفة</p>
                            </div>
                        </div>
                        <a href="" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i>
                            <span>رجوع</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-tag text-blue-600"></i>
                                        اسم الفئة
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-shop text-blue-600"></i>
                                        نسبة التجار
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-tools text-orange-600"></i>
                                        نسبة الفنيين
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-person text-green-600"></i>
                                        نسبة العملاء
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
                            @foreach($categories as $category)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-green-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-tag text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-white">{{ $category->name }}</p>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $category->products_count ?? $category->products->count() }} منتج
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    @if($category->priceRate)
                                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                            {{ $category->priceRate->rate_trade }}%
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">
                                            100%
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($category->priceRate)
                                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">
                                            {{ $category->priceRate->rate_technician }}%
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">
                                            100%
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($category->priceRate)
                                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                            {{ $category->priceRate->rate_client }}%
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 rounded-full text-sm font-medium">
                                            100%
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('categoryPriceRates.create', $category->id) }}" 
                                           class="p-2 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-800/30 transition-colors"
                                           title="تعديل الأسعار">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($category->priceRate)
                                        <form action="{{ route('categoryPriceRates.destroy', $category->id) }}"
                                              method="POST"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف الأسعار"
                                                   onclick="if(confirm('هل أنت متأكد من حذف أسعار هذه الفئة؟')) this.form.submit()">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
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
</x-app-layout>
