<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Daftar')</title>

        {{-- منع وميض الدارك مود قبل تحميل CSS --}}
        <script>
            (function () {
                const t = localStorage.getItem('theme');
                const dark = t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800|figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @vite(['resources/js/category.js'])
        @vite(['resources/js/walletCustomer.js'])
    </head>
    <body class="font-sans antialiased h-full" dir="rtl">
        <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
            @include('layouts.navigation')

            @isset($header)
            <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-sm animate-fade-in-down">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <main class="relative">
                <div class="flex w-full min-h-[calc(100vh-4rem)]">

                    <button id="sidebarToggle"
                            type="button"
                            aria-label="فتح القائمة"
                            class="lg:hidden fixed top-[4.25rem] right-4 z-50 bg-brand-600 hover:bg-brand-700 text-white p-2.5 rounded-xl shadow-lg shadow-brand-500/30 transition-all duration-200 active:scale-95">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <div id="sidebar"
                         class="fixed lg:static inset-y-0 right-0 z-40 top-16 lg:top-0
                                transform translate-x-full lg:translate-x-0
                                transition-transform duration-300 ease-out">
                        @include('layouts.sidebar')
                    </div>

                    <div id="sidebarOverlay"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden hidden transition-opacity duration-300"></div>

                    <div class="w-full min-w-0 flex-1 animate-fade-in">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        @include('sweetalert::alert')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
