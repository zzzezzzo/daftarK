@php
    $isAccountStatements = request()->routeIs('customerAccountStatement.*', 'accountStatement.*', 'customer.invoices.*', 'supplier.invoices.*', 'customer.transactions.*', 'supplier.transactions.*');
@endphp

<div class="sidebar-shell">
    <div class="sidebar-panel flex flex-col h-full">
        <div class="sidebar-brand">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 flex items-center justify-center shadow-md">
                <i class="bi bi-grid-1x2-fill text-white"></i>
            </div>
            <div class="hidden lg:block">
                <p class="font-bold text-slate-800 dark:text-white text-sm">القائمة الرئيسية</p>
                <p class="text-[10px] text-slate-400">تنقل سريع</p>
            </div>
        </div>

        <ul class="space-y-1 flex-1 overflow-y-auto">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-speedometer2 text-lg text-brand-500"></i>
                    <span>لوحة التحكم</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.daily') }}"
                   class="sidebar-link {{ request()->routeIs('reports.daily') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-calendar-day text-lg text-cyan-500"></i>
                    <span>التقرير اليومي</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customer.index') }}"
                   class="sidebar-link {{ request()->routeIs('customer.*') && !request()->routeIs('customer.invoices.*', 'customer.transactions.*') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-people-fill text-lg text-emerald-500"></i>
                    <span>العملاء</span>
                </a>
            </li>
            <li>
                <a href="{{ route('suppliers.index') }}"
                   class="sidebar-link {{ request()->routeIs('suppliers.*', 'supplier.*') && !request()->routeIs('supplier.invoices.*', 'supplier.transactions.*') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-truck text-lg text-amber-500"></i>
                    <span>الموردين</span>
                </a>
            </li>
            <li>
                <a href="{{ route('categories.index') }}"
                   class="sidebar-link {{ request()->routeIs('categories.*') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-tags-fill text-lg text-orange-500"></i>
                    <span>الفئات</span>
                </a>
            </li>

            <li>
                <button type="button"
                        onclick="toggleAccountStatements()"
                        class="sidebar-link w-full {{ $isAccountStatements ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-journal-text text-lg text-violet-500"></i>
                    <span class="flex-1 text-right">كشف الحسابات</span>
                    <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200" id="accountStatementsChevron"></i>
                </button>
                <div id="accountStatementsDropdown" class="{{ $isAccountStatements ? '' : 'hidden' }} mt-1 space-y-0.5 animate-fade-in">
                    <a href="{{ route('customer.index') }}" class="sidebar-sublink">
                        <i class="bi bi-people text-emerald-500"></i><span>قائمة العملاء</span>
                    </a>
                    <a href="{{ route('customer.invoices.index') }}" class="sidebar-sublink">
                        <i class="bi bi-receipt text-brand-500"></i><span>فواتير العملاء</span>
                    </a>
                    <a href="{{ route('customer.transactions.index') }}" class="sidebar-sublink">
                        <i class="bi bi-arrow-left-right text-violet-500"></i><span>معاملات العملاء</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="sidebar-sublink">
                        <i class="bi bi-truck text-amber-500"></i><span>قائمة الموردين</span>
                    </a>
                    <a href="{{ route('supplier.invoices.index') }}" class="sidebar-sublink">
                        <i class="bi bi-file-earmark-text text-orange-500"></i><span>فواتير الموردين</span>
                    </a>
                    <a href="{{ route('supplier.transactions.index') }}" class="sidebar-sublink">
                        <i class="bi bi-exchange text-rose-500"></i><span>معاملات الموردين</span>
                    </a>
                </div>
            </li>

            <li>
                <a href="{{ route('products.index') }}"
                   class="sidebar-link {{ request()->routeIs('products.*') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-box-seam text-lg text-pink-500"></i>
                    <span>المخزون والمنتجات</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cashBoxes.index') }}"
                   class="sidebar-link {{ request()->routeIs('cashBoxes.*') ? 'sidebar-link-active' : '' }}">
                    <i class="bi bi-cash-stack text-lg text-teal-500"></i>
                    <span>الصناديق النقدية</span>
                </a>
            </li>
        </ul>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 hidden lg:block">
            <p class="text-[10px] text-center text-slate-400">محل ابو يزيد © {{ date('Y') }}</p>
        </div>
    </div>
</div>

<script>
function toggleAccountStatements() {
    const dropdown = document.getElementById('accountStatementsDropdown');
    const chevron = document.getElementById('accountStatementsChevron');
    const isHidden = dropdown.classList.contains('hidden');
    dropdown.classList.toggle('hidden', !isHidden);
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}
</script>
