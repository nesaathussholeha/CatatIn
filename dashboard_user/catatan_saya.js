/* catatan_saya.js — khusus halaman catatan_saya.php (menu mobile + toast dari server) */
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

    /* ==========================================================
       TOAST DARI SERVER (pesan sukses/gagal dari PHP)
       ========================================================== */
    function initToastServer() {
        document.querySelectorAll('.toast[data-auto]').forEach(function (el) {
            setTimeout(function () {
                el.classList.add('sembunyi');
                setTimeout(function () { el.remove(); }, 350);
            }, 3000);
        });
    }

    initMenu();
    initToastServer();
})();
