{{-- sidebar  --}}
<div class="w-64 h-full bg-gray-50 dark:bg-gray-900 p-6 lg:p-6">
    <div class="bg-white h-full dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4">
        <!-- Title -->
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 hidden lg:block">القائمة الجانبية</h2>
        
        <!-- Mobile Title (Icon only) -->
        <div class="flex justify-center mb-4 lg:hidden">
            <i class="bi bi-list text-gray-600 dark:text-gray-400 text-xl"></i>
        </div>
        
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-blue-100 dark:hover:bg-blue-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-200 dark:bg-blue-600 font-semibold text-blue-800 dark:text-blue-100' : '' }}">
                    <i class="bi bi-house-door-fill text-blue-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">لوحة التحكم</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-green-100 dark:hover:bg-green-700 transition-colors {{ request()->routeIs('customer.index') ? 'bg-green-200 dark:bg-green-600 font-semibold text-green-800 dark:text-green-100' : '' }}">
                    <i class="bi bi-people-fill text-green-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">العملاء</span>
                </a>
            </li>
            <li>
                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-yellow-100 dark:hover:bg-yellow-700 transition-colors {{ request()->routeIs('suppliers.index') ? 'bg-yellow-200 dark:bg-yellow-600 font-semibold text-yellow-800 dark:text-yellow-100' : '' }}">
                    <i class="bi bi-truck text-yellow-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">الموردين</span>
                </a>
            </li>
            <li>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-orange-100 dark:hover:bg-orange-700 transition-colors {{ request()->routeIs('categories.index') ? 'bg-orange-200 dark:bg-orange-600 font-semibold text-orange-800 dark:text-orange-100' : '' }}">
                    <i class="bi bi-tags text-orange-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">الفئات</span>
                </a>
            </li>
            <li>
                <div class="relative">
                    <button onclick="toggleAccountStatements()" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-purple-100 dark:hover:bg-purple-700 transition-colors w-full text-left {{ request()->routeIs('customerAccountStatement.*') || request()->routeIs('accountStatement.*') ? 'bg-purple-200 dark:bg-purple-600 font-semibold text-purple-800 dark:text-purple-100' : '' }}">
                        <i class="bi bi-journal-text text-purple-500 text-lg flex-shrink-0"></i>
                        <span class="lg:inline">كشف الحسابات</span>
                        <i class="bi bi-chevron-left text-gray-400 mr-auto transition-transform" id="accountStatementsChevron"></i>
                    </button>
                    <div id="accountStatementsDropdown" class="hidden mt-1 space-y-1">
                        <a href="{{ route('customer.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-800 transition-colors text-sm">
                            <i class="bi bi-people text-green-500 text-sm"></i>
                            <span>قائمة العملاء</span>
                        </a>
                        <a href="{{ route('customer.invoices.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-800 transition-colors text-sm">
                            <i class="bi bi-receipt text-blue-500 text-sm"></i>
                            <span>جميع فواتير العملاء</span>
                        </a>
                        <a href="{{ route('customer.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-800 transition-colors text-sm">
                            <i class="bi bi-arrow-left-right text-purple-500 text-sm"></i>
                            <span>معاملات العملاء (لكل عميل)</span>
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-800 transition-colors text-sm">
                            <i class="bi bi-truck text-yellow-500 text-sm"></i>
                            <span>قائمة الموردين</span>
                        </a>
                        <a href="{{ route('supplier.invoices.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-800 transition-colors text-sm">
                            <i class="bi bi-file-earmark-text text-orange-500 text-sm"></i>
                            <span>جميع فواتير الموردين</span>
                        </a>
                        <a href="" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-600 transition-colors text-sm">
                            <i class="bi bi-cash-stack text-red-500 text-sm"></i>
                            <span>سجلات الموردين</span>
                        </a>
                        <a href="{{ route('supplier.transactions.index') }}" class="flex items-center gap-3 px-8 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-800 transition-colors text-sm">
                            <i class="bi bi-exchange text-red-500 text-sm"></i>
                            <span>معاملات الموردين</span>
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-pink-100 dark:hover:bg-pink-700 transition-colors {{ request()->routeIs('products.index') ? 'bg-pink-200 dark:bg-pink-600 font-semibold text-pink-800 dark:text-pink-100' : '' }}">
                    <i class="bi bi-box-seam text-pink-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">المخزون والمنتجات</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cashBoxes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md text-gray-200 hover:bg-teal-100 dark:hover:bg-teal-700 transition-colors {{ request()->routeIs('cashBoxes.index') ? 'bg-teal-200 dark:bg-teal-600 font-semibold text-teal-800 dark:text-teal-100' : '' }}">
                    <i class="bi bi-cash-stack text-teal-500 text-lg flex-shrink-0"></i>
                    <span class=" lg:inline">الصناديق النقدية</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
function toggleAccountStatements() {
    const dropdown = document.getElementById('accountStatementsDropdown');
    const chevron = document.getElementById('accountStatementsChevron');
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        chevron.style.transform = 'rotate(-90deg)';
    } else {
        dropdown.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}
</script>