const STORAGE_KEY = 'theme';

export function getTheme() {
    if (localStorage.getItem(STORAGE_KEY) === 'dark') return 'dark';
    if (localStorage.getItem(STORAGE_KEY) === 'light') return 'light';
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;

    document.querySelectorAll('[data-theme-icon="sun"]').forEach((el) => {
        el.classList.toggle('hidden', theme === 'dark');
    });
    document.querySelectorAll('[data-theme-icon="moon"]').forEach((el) => {
        el.classList.toggle('hidden', theme !== 'dark');
    });
}

export function toggleTheme() {
    const next = getTheme() === 'dark' ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
}

// Apply before paint (also called inline in layout head)
applyTheme(getTheme());

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', toggleTheme);
    });
});
