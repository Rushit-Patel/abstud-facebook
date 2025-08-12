// Team Layout JavaScript
// This file contains JavaScript for the team layout components

// Initialize theme switching
document.addEventListener('DOMContentLoaded', function() {
    // Theme switching functionality
    const themeToggle = document.querySelector('[data-kt-theme-switch-toggle]');
    if (themeToggle) {
        themeToggle.addEventListener('change', function() {
            const isDark = this.checked;
            document.documentElement.setAttribute('data-kt-theme-mode', isDark ? 'dark' : 'light');
            localStorage.setItem('kt-theme', isDark ? 'dark' : 'light');
        });
    }

    // Initialize current theme state
    const currentTheme = localStorage.getItem('kt-theme') || 'light';
    if (themeToggle) {
        themeToggle.checked = currentTheme === 'dark';
    }
});
