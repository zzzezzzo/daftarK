<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Daftar')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @vite(['resources/js/category.js'])
        @vite(['resources/js/walletCustomer.js'])
    </head>
    <body class="font-sans antialiased" dir="rtl">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
            
            <!-- Page Heading -->
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset
            <!-- Page Content -->
            <main>
                <div class="flex w-full relative">
                    <!-- Toggle Button for Mobile -->
                    <button id="sidebarToggle" class="lg:hidden fixed top-4 right-4 z-50 bg-gray-800 text-white p-2 rounded-lg shadow-lg">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    
                    <!-- Sidebar -->
                    <div id="sidebar" class="fixed lg:static inset-y-0 right-0 z-40 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
                        @include('layouts.sidebar')
                    </div>
                    
                    <!-- Overlay for Mobile -->
                    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>
                    
                    <!-- Main Content -->
                    <div class="w-full lg:mr-0 mr-0">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        @include('sweetalert::alert')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
