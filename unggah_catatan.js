/* upload.js — khusus halaman upload.php (menu mobile) */
(function () {
    'use strict';

    /* ==========================================================
       MENU MOBILE (hamburger + sidebar)
       ========================================================== */
    function initMenu() {
        var sidebar     = document.getElementById('sidebar');
        var overlay     = document.getElementById('overlay');
        var tombolMenu  = document.getElementById('tombol-menu');
        var tombolTutup = document.getElementById('tutup-menu');

        if (!sidebar || !overlay || !tombolMenu || !tombolTutup) { return; }

        function atur(buka, kembalikanFokus) {
            sidebar.classList.toggle('terbuka', buka);
            overlay.classList.toggle('tampil', buka);
            tombolMenu.setAttribute('aria-expanded', String(buka));
            document.body.style.overflow = buka ? 'hidden' : '';
            if (buka) {
                tombolTutup.focus();
            } else if (kembalikanFokus) {
                tombolMenu.focus();
            }
        }

        tombolMenu.addEventListener('click', function () { atur(true); });
        tombolTutup.addEventListener('click', function () { atur(false, true); });
        overlay.addEventListener('click', function () { atur(false, true); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('terbuka')) {
                atur(false, true);
            }
        });

        // Klik salah satu menu: tutup panel
        sidebar.addEventListener('click', function (e) {
            if (e.target.closest('.menu a')) { atur(false, false); }
        });

        // Layar dilebarkan ke desktop saat menu terbuka: reset keadaan
        window.addEventListener('resize', function () {
            if (window.innerWidth > 860 && sidebar.classList.contains('terbuka')) {
                atur(false, false);
            }
        });
    }

    initMenu();
})();