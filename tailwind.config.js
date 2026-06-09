import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                surface: {
                    light: '#f8fafc',
                    dark: '#0f172a',
                    card: '#ffffff',
                    'card-dark': '#1e293b',
                },
            },
            boxShadow: {
                soft: '0 4px 24px -4px rgba(15, 23, 42, 0.08)',
                'soft-dark': '0 4px 24px -4px rgba(0, 0, 0, 0.35)',
                glow: '0 0 20px -5px rgba(59, 130, 246, 0.4)',
            },
            animation: {
                'fade-in': 'fadeIn 0.45s ease-out both',
                'fade-in-up': 'fadeInUp 0.5s ease-out both',
                'fade-in-down': 'fadeInDown 0.4s ease-out both',
                'scale-in': 'scaleIn 0.35s ease-out both',
                'slide-in-right': 'slideInRight 0.35s ease-out both',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(14px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(12px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
