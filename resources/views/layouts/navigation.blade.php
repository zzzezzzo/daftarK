<nav x-data="{ open: false }"
     class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/85 backdrop-blur-lg border-b border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
    <div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center gap-4">

            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 flex items-center justify-center shadow-md shadow-brand-500/25 group-hover:scale-105 transition-transform duration-200">
                        <i class="bi bi-journal-bookmark-fill text-white text-sm"></i>
                    </div>
                    <div class="hidden sm:block">
                        <span class="font-bold text-slate-800 dark:text-white text-lg leading-tight">دفتر</span>
                        <span class="block text-[10px] text-slate-400 dark:text-slate-500 -mt-0.5">نظام إدارة الفواتير</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-1 mr-2">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('dashboard')
                                  ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300'
                                  : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <i class="bi bi-speedometer2 ml-1"></i>
                        لوحة التحكم
                    </a>
                    <a href="{{ route('customer.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('customer.*')
                                  ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
                                  : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <i class="bi bi-people ml-1"></i>
                        العملاء
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Dark mode toggle --}}
                <button type="button"
                        data-theme-toggle
                        aria-label="تبديل الوضع الليلي"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                               bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300
                               hover:bg-slate-100 dark:hover:bg-slate-700
                               transition-all duration-200 active:scale-95">
                    <i class="bi bi-sun-fill text-lg" data-theme-icon="sun"></i>
                    <i class="bi bi-moon-stars-fill text-lg hidden" data-theme-icon="moon"></i>
                </button>

                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-400 to-violet-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="bi bi-person ml-1"></i> الملف الشخصي
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right ml-1"></i> تسجيل الخروج
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <button @click="open = !open"
                        class="sm:hidden p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="bi bi-list text-xl" x-show="!open"></i>
                    <i class="bi bi-x-lg text-xl" x-show="open" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 animate-fade-in-down">
        <div class="pt-2 pb-3 px-4 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">لوحة التحكم</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customer.index')" :active="request()->routeIs('customer.*')">العملاء</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">الفئات</x-responsive-nav-link>
        </div>
        <div class="pt-3 pb-4 border-t border-slate-200 dark:border-slate-700 px-4">
            <p class="font-medium text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</p>
            <p class="text-sm text-slate-500">{{ Auth::user()->email }}</p>
        </div>
    </div>
</nav>
