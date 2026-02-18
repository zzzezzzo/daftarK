@auth()
<div class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo Section -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="bi bi-shop text-white text-xl"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold text-gray-900 dark:text-white">نظام إدارة المتجر</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">نظام متكامل لإدارة المخزون والمبيعات</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 space-x-reverse">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('dashboard') 
                              ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' 
                              : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span>لوحة التحكم</span>
                </a>

                <!-- Products -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700
                               group-hover:bg-blue-100 group-hover:text-blue-700 dark:group-hover:bg-blue-900/20 dark:group-hover:text-blue-400">
                        <i class="bi bi-box text-lg"></i>
                        <span>المنتجات</span>
                        <i class="bi bi-chevron-down text-xs mr-1"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('products.index') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700">
                                <i class="bi bi-list-ul"></i>
                                <span>قائمة المنتجات</span>
                            </a>
                            <a href="{{ route('products.create') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700">
                                <i class="bi bi-plus-lg"></i>
                                <span>إضافة منتج</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700
                               group-hover:bg-purple-100 group-hover:text-purple-700 dark:group-hover:bg-purple-900/20 dark:group-hover:text-purple-400">
                        <i class="bi bi-tags text-lg"></i>
                        <span>الفئات</span>
                        <i class="bi bi-chevron-down text-xs mr-1"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('categories.index') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700">
                                <i class="bi bi-list-ul"></i>
                                <span>قائمة الفئات</span>
                            </a>
                            <a href="{{ route('categoryPriceRates.index') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700">
                                <i class="bi bi-percent"></i>
                                <span>إدارة الأسعار</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700
                               group-hover:bg-green-100 group-hover:text-green-700 dark:group-hover:bg-green-900/20 dark:group-hover:text-green-400">
                        <i class="bi bi-people text-lg"></i>
                        <span>العملاء</span>
                        <i class="bi bi-chevron-down text-xs mr-1"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('customer.index') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-700">
                                <i class="bi bi-list-ul"></i>
                                <span>قائمة العملاء</span>
                            </a>
                            <a href="{{ route('customer.create') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-700">
                                <i class="bi bi-person-plus"></i>
                                <span>إضافة عميل</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Suppliers -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700
                               group-hover:bg-orange-100 group-hover:text-orange-700 dark:group-hover:bg-orange-900/20 dark:group-hover:text-orange-400">
                        <i class="bi bi-truck text-lg"></i>
                        <span>الموردون</span>
                        <i class="bi bi-chevron-down text-xs mr-1"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('suppliers.index') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-gray-700">
                                <i class="bi bi-list-ul"></i>
                                <span>قائمة الموردين</span>
                            </a>
                            <a href="{{ route('suppliers.create') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-gray-700">
                                <i class="bi bi-person-plus"></i>
                                <span>إضافة مورد</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reports -->
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700
                               group-hover:bg-red-100 group-hover:text-red-700 dark:group-hover:bg-red-900/20 dark:group-hover:text-red-400">
                        <i class="bi bi-graph-up text-lg"></i>
                        <span>التقارير</span>
                        <i class="bi bi-chevron-down text-xs mr-1"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="#" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>تقرير المبيعات</span>
                            </a>
                            <a href="#" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-700">
                                <i class="bi bi-cash-stack"></i>
                                <span>تقرير المالية</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="bi bi-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- User Dropdown -->
                <div class="relative group">
                    <button class="flex items-center gap-3 p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                            <i class="bi bi-person text-white"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">مدير النظام</div>
                        </div>
                        <i class="bi bi-chevron-down text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="bi bi-person"></i>
                                <span>الملف الشخصي</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" 
                                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 w-full text-right">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>تسجيل الخروج</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Menu Button -->
<div class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="bi bi-shop text-white"></i>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">المتجر</span>
            </div>
            
            <button onclick="toggleMobileMenu()" class="p-2 text-gray-600 dark:text-gray-400">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </div>
</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 w-64 h-full shadow-xl">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="bi bi-shop text-white text-xl"></i>
                </div>
                <div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">نظام إدارة المتجر</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="bi bi-grid"></i>
                <span>لوحة التحكم</span>
            </a>
            
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="bi bi-box"></i>
                <span>المنتجات</span>
            </a>
            
            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="bi bi-tags"></i>
                <span>الفئات</span>
            </a>
            
            <a href="{{ route('customer.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="bi bi-people"></i>
                <span>العملاء</span>
            </a>
            
            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="bi bi-truck"></i>
                <span>الموردون</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 w-full">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </nav>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        if (!menu.contains(event.target) && !event.target.closest('#mobileMenu')) {
            menu.classList.add('hidden');
        }
    });
</script>
</div>
@endif
