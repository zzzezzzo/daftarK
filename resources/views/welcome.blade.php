<!DOCTYPE html>

<html class="light" dir="rtl" lang="ar"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الخياط المحترف - شاشة الترحيب</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter", "system-ui", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-overlay {
            background: linear-gradient(rgba(16, 25, 34, 0.6), rgba(16, 25, 34, 0.85));
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 overflow-hidden">
<!-- Main Landing Container -->
<div class="relative h-screen w-full flex items-center justify-center overflow-hidden bg-slate-900">
<!-- Hero Image Background -->
<div class="absolute inset-0 z-0">
<img alt="Modern sewing machine on a professional workbench" class="w-full h-full object-cover" data-alt="Modern sewing machine on a professional workbench" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBvQ0CQOP_R3syVN_P4OZ-ajqGrmubdhBkm0Dhn2RnWYadBa8WULUQNB19o6-FIqOO5mFF4KW2VJ9UIOPR6RXffH2fPIDytIsNggplHI27X6N6v6fbuZGZ92xBf5UaEerPt92Wx9g-2Ez7L2IKOz0sEXvB4YUC9-IFwP3Z_BlPMi7-YUq1tBLiN0X9IXDE0O0Yj9waPShwVUGTzxRLZENgF7Is0D7-2IL1KxBk3gxIyWeXCYuA_q4aOpR957BJBQO3y4f0GmmClLQ"/>
<div class="absolute inset-0 hero-overlay"></div>
</div>

<main class="relative z-10 w-full max-w-5xl px-6 text-center">

<!-- Action Card -->
<div class="order-1 lg:order-2 flex justify-center lg:justify-end">
<div class="glass-card p-8 lg:p-10 rounded-2xl w-full max-w-md shadow-2xl">
<div class="flex flex-col items-center text-center gap-8">
<div class="size-20 bg-primary/20 rounded-2xl flex items-center justify-center border border-primary/30">
<span class="material-symbols-outlined text-primary text-4xl">inventory_2</span>
</div>
<div class="space-y-2">
<h2 class="text-2xl font-bold text-white tracking-tight">مرحباً بك مجدداً</h2>
<p class="text-slate-400 text-sm">ابدأ يومك بإدارة مخزونك ومبيعاتك بكفاءة</p>
</div>
<div class="flex flex-col w-full gap-4">
<!-- Primary Login Button -->
<a href="{{ route('login') }}" class="w-full h-14 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-3 shadow-lg shadow-primary/20 group">
<span>تسجيل الدخول</span>
<span class="material-symbols-outlined group-hover:translate-x-[-4px] transition-transform">login</span>
</a>
<!-- Secondary Register Button -->
<a href="{{ route('register') }}" class="w-full h-14 bg-transparent border border-slate-700 hover:border-slate-500 hover:bg-white/5 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-3">
<span>إنشاء حساب جديد</span>
<span class="material-symbols-outlined">person_add</span>
</a>
</div>
<div class="flex flex-col gap-4 w-full">
<div class="relative flex items-center justify-center py-2">
<div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-800"></div>
</div>
</div>
</div>
</div>
</main>
<!-- Subtle Footer -->

<!-- Decorative elements -->
<div class="absolute top-1/4 -right-20 w-80 h-80 bg-primary/20 rounded-full blur-[120px] pointer-events-none"></div>
<div class="absolute bottom-1/4 -left-20 w-80 h-80 bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
</div>
</body></html>