/* playlist.js — interaksi halaman Koleksi Belajar (menu, dialog, konfirmasi, toast) */
(function () {
    'use strict';

    /* ---------- Menu hamburger (HP / tablet) ---------- */
    var tombolMenu = document.getElementById('tombol-menu');
    var tombolTutup = document.getElementById('tutup-menu');
    var overlay = document.getElementById('overlay');

    function bukaMenu() {
        document.body.classList.add('menu-terbuka');
        tombolMenu.setAttribute('aria-expanded', 'true');
        tombolTutup.focus();
    }

    function tutupMenuFn(kembaliFokus) {
        document.body.classList.remove('menu-terbuka');
        tombolMenu.setAttribute('aria-expanded', 'false');
        if (kembaliFokus) { tombolMenu.focus(); }
    }

    tombolMenu.addEventListener('click', bukaMenu);
    tombolTutup.addEventListener('click', function () { tutupMenuFn(true); });
    overlay.addEventListener('click', function () { tutupMenuFn(true); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.body.classList.contains('menu-terbuka')) { tutupMenuFn(true); }
    });

    /* Layar melebar ke desktop: tutup laci otomatis */
    window.matchMedia('(min-width: 1025px)').addEventListener('change', function (e) {
        if (e.matches) { tutupMenuFn(false); }
    });

    /* ---------- Toast dari server (setelah simpan / hapus) ---------- */
    function pasangAutoHide(el, ms) {
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, ms);
    }

    document.querySelectorAll('.toast').forEach(function (el) { pasangAutoHide(el, 4000); });

    /* ---------- Menu titik tiga di kartu folder ---------- */
    function tutupMenuFolder(kecuali) {
        document.querySelectorAll('.menu-titik[aria-expanded="true"]').forEach(function (b) {
            if (b !== kecuali) {
                b.setAttribute('aria-expanded', 'false');
                b.nextElementSibling.hidden = true;
            }
        });
    }

    document.addEventListener('click', function (e) {
        var tombol = e.target.closest('.menu-titik');
        if (tombol) {
            var akanBuka = tombol.getAttribute('aria-expanded') !== 'true';
            tutupMenuFolder(tombol);
            tombol.setAttribute('aria-expanded', String(akanBuka));
            tombol.nextElementSibling.hidden = !akanBuka;
            return;
        }
        /* Klik di mana pun (termasuk memilih item menu) menutup menu */
        tutupMenuFolder(null);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        var terbuka = document.querySelector('.menu-titik[aria-expanded="true"]');
        if (terbuka) { tutupMenuFolder(null); terbuka.focus(); }
    });

    /* ---------- Dialog: buka / tutup ---------- */
    document.addEventListener('click', function (e) {
        var buka = e.target.closest('[data-buka]');
        if (buka) {
            var dlg = document.getElementById(buka.dataset.buka);
            if (dlg && typeof dlg.showModal === 'function') {
                /* Isi dialog sesuai folder yang menunya diklik */
                if (buka.dataset.folderId) {
                    var idInput = dlg.querySelector('input[name="folder_id"]');
                    if (idInput) { idInput.value = buka.dataset.folderId; }

                    var namaInput = dlg.querySelector('input[name="nama"]');
                    if (namaInput) { namaInput.value = buka.dataset.folderNama; }

                    dlg.querySelectorAll('[data-isi-nama]').forEach(function (el) {
                        el.textContent = buka.dataset.folderNama;
                    });
                }
                dlg.showModal();
                var isian = dlg.querySelector('input[type="text"]');
                if (isian) { isian.focus(); isian.select(); }
            }
            return;
        }

        if (e.target.closest('[data-tutup]')) {
            e.target.closest('dialog').close();
            return;
        }

        /* Klik di area gelap (backdrop) menutup dialog */
        if (e.target instanceof HTMLDialogElement) { e.target.close(); }
    });

    /* ---------- Konfirmasi sebelum mengeluarkan catatan ---------- */
    document.addEventListener('submit', function (e) {
        var pesan = e.target.dataset ? e.target.dataset.konfirm : '';
        if (pesan && !window.confirm(pesan)) { e.preventDefault(); }
    });
})();