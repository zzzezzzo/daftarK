<x-app-layout>
    <div class="page-shell">
        <div class="page-container">
            <div class="ui-card overflow-hidden mb-6 animate-fade-in-up">
                <div class="ui-card-header">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-exclamation-circle text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">المنتجات غير المتوفرة</h1>
                                <p class="text-blue-100/90 text-sm">المنتجات التي نفدت بعد حركة فعلية على المخزون</p>
                            </div>
                        </div>
                        <a href="{{ route('products.index') }}" class="ui-btn-ghost">
                            <i class="bi bi-box-seam"></i>
                            كل المنتجات
                        </a>
                    </div>
                </div>
                <div class="p-5">
                    <form action="{{ route('products.out-of-stock') }}" method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3">
                        <div class="flex-1">
                            <label for="category_id" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">الفئة</label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="flex-1 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="">كل الفئات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                        <div>
                            <label for="from_date" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">نفد من تاريخ</label>
                            <input
                                type="date"
                                id="from_date"
                                name="from_date"
                                value="{{ request('from_date') }}"
                                class="rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-brand-500 focus:ring-brand-500"
                            >
                        </div>
                        <div>
                            <label for="to_date" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">إلى تاريخ</label>
                            <input
                                type="date"
                                id="to_date"
                                name="to_date"
                                value="{{ request('to_date') }}"
                                class="rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-brand-500 focus:ring-brand-500"
                            >
                        </div>
                        <button type="submit" class="ui-btn-primary justify-center">
                            <i class="bi bi-funnel"></i>
                            تصفية
                        </button>
                        @if(request()->filled('category_id') || request()->filled('from_date') || request()->filled('to_date'))
                            <a href="{{ route('products.out-of-stock') }}" class="ui-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 justify-center">
                                <i class="bi bi-x-circle"></i>
                                إلغاء الفلتر
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="ui-card overflow-hidden">
                <div class="flex items-center justify-between gap-3 p-5 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="font-bold text-slate-800 dark:text-white">قائمة المنتجات</h2>
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm font-semibold">
                        {{ $products->total() }} منتج
                    </span>
                </div>

                @if($products->isEmpty())
                    <div class="p-10 text-center">
                        <i class="bi bi-check-circle text-4xl text-emerald-500"></i>
                        <p class="mt-3 font-semibold text-slate-700 dark:text-slate-200">لا توجد منتجات منتهية الكمية في هذه الفئة</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 dark:bg-slate-700/70 text-slate-600 dark:text-slate-200">
                                <tr>
                                    <th class="p-4 text-right font-semibold">الكود</th>
                                    <th class="p-4 text-right font-semibold">اسم المنتج</th>
                                    <th class="p-4 text-right font-semibold">الفئة</th>
                                    <th class="p-4 text-center font-semibold">تاريخ النفاد</th>
                                    <th class="p-4 text-center font-semibold">الكمية</th>
                                    <th class="p-4 text-center font-semibold">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="p-4">
                                            <span class="font-mono text-sm bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">{{ $product->code }}</span>
                                        </td>
                                        <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $product->name }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-300">{{ $product->category?->name ?? 'بدون فئة' }}</td>
                                        <td class="p-4 text-center text-slate-600 dark:text-slate-300">{{ $product->depleted_at }}</td>
                                        <td class="p-4 text-center">
                                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm font-semibold">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('products.edit', $product->id) }}" class="ui-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200" title="تعديل المنتج">
                                                    <i class="bi bi-pencil-square"></i>
                                                    تعديل
                                                </a>
                                                <a href="{{ route('products.movements', $product->id) }}" class="ui-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200" title="حركات المنتج">
                                                    <i class="bi bi-arrow-left-right"></i>
                                                    الحركات
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($products->hasPages())
                        <div class="p-5 border-t border-slate-100 dark:border-slate-700">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
