<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-tags text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">الفئات</h1>
                                <p class="text-purple-100 text-sm">إدارة فئات المنتجات ونسب التسعير</p>
                            </div>
                        </div>
                        <a href="{{ route('categoryPriceRates.index') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-percent"></i>
                            <span>إدارة الأسعار</span>
                        </a>
                        <a href="{{ route('products.create') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>منتج جديد</span>
                        </a>
                        <button id="addCategoryBtn" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>إضافة فئة جديدة</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <form action="{{ route('categories.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="ابحث عن فئة بالاسم..." 
                            class="w-full pr-12 pl-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </form>
            </div>

            <!-- Categories Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-4 text-right font-semibold">
                                    <div class="flex items-center gap-2 justify-end">
                                        <i class="bi bi-tag text-purple-600"></i>
                                        اسم الفئة
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-percent text-purple-600"></i>
                                        نسبة التجار
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-tools text-purple-600"></i>
                                        نسبة الفنيين
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-person text-purple-600"></i>
                                        نسبة العملاء
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-box text-purple-600"></i>
                                        عدد المنتجات
                                    </div>
                                </th>
                                <th class="p-4 text-center font-semibold">
                                    <div class="flex items-center gap-2 justify-center">
                                        <i class="bi bi-gear text-purple-600"></i>
                                        إجراءات
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-tag text-purple-600"></i>
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
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-full text-sm font-medium">
                                        {{ $category->products_count ?? $category->products->count() }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('categoryPriceRates.create', $category->id) }}" 
                                           class="p-2 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-800/30 transition-colors"
                                           title="تعديل الأسعار">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category->id) }}" 
                                           class="p-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                                           title="تعديل الفئة">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $category->id) }}" 
                                              method="POST"
                                              class="inline"
                                              data-id="{{ $category->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                   class="p-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/30 transition-colors"
                                                   title="حذف الفئة"
                                                   onclick="if(confirm('هل أنت متأكد من حذف هذه الفئة؟')) this.form.submit()">
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
                @if($categories->hasPages())
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        {{ $categories->links() }}
                    </div>
                @endif
                </div>
        </div>
    </div>

    <script>
        const url = "{{ route('categories.store') }}";        
        window.categoryConfig = {
            url: url,
            csrf : "{{ csrf_token() }}"
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>
