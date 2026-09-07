/**
 * PresensiKita — Global Theme Toggle (Dark & Light Mode)
 * Persisten di seluruh halaman menggunakan localStorage.
 */
(function() {
    // 1. Inisialisasi tema saat script dimuat
    function getStoredTheme() {
        var saved = localStorage.getItem('presensikita_theme');
        if (saved === 'dark' || saved === 'light') return saved;
        return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('presensikita_theme', theme);
        updateToggleButtons(theme);
    }

    function updateToggleButtons(theme) {
        var buttons = document.querySelectorAll('.theme-toggle-btn');
        buttons.forEach(function(btn) {
            var iconSun = btn.querySelector('.theme-icon-sun');
            var iconMoon = btn.querySelector('.theme-icon-moon');
            if (iconSun && iconMoon) {
                if (theme === 'dark') {
                    iconSun.style.display = 'block';
                    iconMoon.style.display = 'none';
                    btn.setAttribute('title', 'Beralih ke Mode Terang (Light)');
                    btn.setAttribute('aria-label', 'Beralih ke Mode Terang');
                } else {
                    iconSun.style.display = 'none';
                    iconMoon.style.display = 'block';
                    btn.setAttribute('title', 'Beralih ke Mode Gelap (Dark)');
                    btn.setAttribute('aria-label', 'Beralih ke Mode Gelap');
                }
            }
        });
    }

    // Ekspor fungsi global
    window.toggleDarkMode = function() {
        var current = document.documentElement.getAttribute('data-theme') || getStoredTheme();
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);

        // Dispatch custom event untuk komponen atau chart yang perlu update warna
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
    };

    // Jalankan update button setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        var current = document.documentElement.getAttribute('data-theme') || getStoredTheme();
        applyTheme(current);
    });
})();
