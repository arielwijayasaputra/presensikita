/**
 * PresensiKita — Global Theme Toggle (Dark & Light Mode)
 * Persisten di seluruh halaman menggunakan localStorage dengan transisi animasi halus & modern.
 */
(function() {
    function getStoredTheme() {
        var saved = localStorage.getItem('presensikita_theme');
        if (saved === 'dark' || saved === 'light') return saved;
        return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }

    function applyTheme(theme, isUserAction) {
        var doc = document.documentElement;

        if (isUserAction) {
            doc.classList.add('theme-transitioning');
            clearTimeout(window.__themeTransitionTimer);
            window.__themeTransitionTimer = setTimeout(function() {
                doc.classList.remove('theme-transitioning');
            }, 450);
        }

        doc.setAttribute('data-theme', theme);
        localStorage.setItem('presensikita_theme', theme);
        updateToggleButtons(theme, isUserAction);
    }

    function updateToggleButtons(theme, isUserAction) {
        var buttons = document.querySelectorAll('.theme-toggle-btn');
        buttons.forEach(function(btn) {
            // Bersihkan inline display style agar CSS animasi transform & opacity berjalan penuh
            var iconSun = btn.querySelector('.theme-icon-sun');
            var iconMoon = btn.querySelector('.theme-icon-moon');
            if (iconSun && iconSun.style.display) iconSun.style.display = '';
            if (iconMoon && iconMoon.style.display) iconMoon.style.display = '';

            if (isUserAction) {
                btn.classList.remove('btn-theme-animating');
                void btn.offsetWidth; // Force reflow untuk me-restart animasi pulse
                btn.classList.add('btn-theme-animating');
                setTimeout(function() {
                    btn.classList.remove('btn-theme-animating');
                }, 450);
            }

            if (theme === 'dark') {
                btn.setAttribute('title', 'Beralih ke Mode Terang (Light Mode)');
                btn.setAttribute('aria-label', 'Beralih ke Mode Terang');
            } else {
                btn.setAttribute('title', 'Beralih ke Mode Gelap (Dark Mode)');
                btn.setAttribute('aria-label', 'Beralih ke Mode Gelap');
            }
        });
    }

    // Ekspor fungsi global
    window.toggleDarkMode = function() {
        var doc = document.documentElement;
        var current = doc.getAttribute('data-theme') || getStoredTheme();
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next, true);

        // Dispatch custom event untuk komponen atau chart yang perlu update warna
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
    };

    // Jalankan inisialisasi awal
    var current = getStoredTheme();
    document.documentElement.setAttribute('data-theme', current);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateToggleButtons(current, false);
        });
    } else {
        updateToggleButtons(current, false);
    }
})();
