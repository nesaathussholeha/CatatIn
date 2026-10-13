document.addEventListener("DOMContentLoaded", function () {
    /* ---------- Sidebar Navigasi ---------- */
    var menuToggle = document.getElementById("menuToggle");
    var closeSidebar = document.getElementById("closeSidebar");
    var sidebar = document.getElementById("sidebar");
    var overlay = document.getElementById("sidebarOverlay");

    function openMenu() {
        if (sidebar) sidebar.classList.add("active");
        if (overlay) overlay.classList.add("active");
    }

    function closeMenu() {
        if (sidebar) sidebar.classList.remove("active");
        if (overlay) overlay.classList.remove("active");
    }

    if (menuToggle) menuToggle.addEventListener("click", openMenu);
    if (closeSidebar) closeSidebar.addEventListener("click", closeMenu);
    if (overlay) overlay.addEventListener("click", closeMenu);

    /* ---------- Auto Hide Toast ---------- */
    function pasangAutoHide(el, ms) {
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, ms);
    }

    document.querySelectorAll('.toast').forEach(function (el) { 
        pasangAutoHide(el, 4000); 
    });

    /* ---------- Menu Titik Tiga (Opsi Folder) ---------- */
    function tutupMenuFolder(kecuali) {
        document.querySelectorAll('.menu-titik[aria-expanded="true"]').forEach(function (b) {
            if (b !== kecuali) {
                b.setAttribute('aria-expanded', 'false');
                if (b.nextElementSibling) b.nextElementSibling.hidden = true;
            }
        });
    }

    document.addEventListener('click', function (e) {
        var tombol = e.target.closest('.menu-titik');
        if (tombol) {
            var akanBuka = tombol.getAttribute('aria-expanded') !== 'true';
            tutupMenuFolder(tombol);
            tombol.setAttribute('aria-expanded', String(akanBuka));
            if (tombol.nextElementSibling) tombol.nextElementSibling.hidden = !akanBuka;
            return;
        }
        tutupMenuFolder(null);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        var terbuka = document.querySelector('.menu-titik[aria-expanded="true"]');
        if (terbuka) { 
            tutupMenuFolder(null); 
            terbuka.focus(); 
        }
    });

    /* ---------- Modal Dialog ---------- */
    document.addEventListener('click', function (e) {
        var buka = e.target.closest('[data-buka]');
        if (buka) {
            var dlg = document.getElementById(buka.dataset.buka);
            if (dlg && typeof dlg.showModal === 'function') {
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
            var dialog = e.target.closest('dialog');
            if (dialog) dialog.close();
            return;
        }

        if (e.target instanceof HTMLDialogElement) { 
            e.target.close(); 
        }
    });

    /* ---------- Konfirmasi Form ---------- */
    document.addEventListener('submit', function (e) {
        var pesan = e.target.dataset ? e.target.dataset.konfirm : '';
        if (pesan && !window.confirm(pesan)) { 
            e.preventDefault(); 
        }
    });
});