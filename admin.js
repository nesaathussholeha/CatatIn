(function () {
    'use strict';

    /* ==========================================================
       SIDEBAR (menu di HP)
       ========================================================== */
    var sidebar   = document.getElementById('sidebar');
    var overlay   = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('menuToggle');

    function bukaSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    }

    function tutupSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.contains('open') ? tutupSidebar() : bukaSidebar();
    });
    overlay.addEventListener('click', tutupSidebar);

    window.addEventListener('resize', function () {
        if (window.innerWidth > 640) tutupSidebar();
    });

    /* ==========================================================
       MODAL
       ========================================================== */
    function bukaModal(modal) {
        modal.classList.add('tampil');
        modal.setAttribute('aria-hidden', 'false');
        var pertama = modal.querySelector('input:not([type="hidden"]), select');
        if (pertama) pertama.focus();
    }

    function tutupSemuaModal() {
        document.querySelectorAll('.modal-latar.tampil').forEach(function (m) {
            m.classList.remove('tampil');
            m.setAttribute('aria-hidden', 'true');
        });
    }

    // Klik area gelap = tutup modal
    document.querySelectorAll('.modal-latar').forEach(function (m) {
        m.addEventListener('click', function (e) {
            if (e.target === m) tutupSemuaModal();
        });
    });

    // Tekan Esc = tutup modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            tutupSemuaModal();
            tutupDropdown();
        }
    });

    /* ==========================================================
       TAMBAH & EDIT (form di modal dipakai bersama)
       ========================================================== */
    var konfigForm = {
        catatan:  { modal: 'modalCatatan',  nama: 'catatan'  },
        pengguna: { modal: 'modalPengguna', nama: 'pengguna' }
    };

    function siapkanForm(entitas, mode, data) {
        var cfg   = konfigForm[entitas];
        var modal = document.getElementById(cfg.modal);
        var form  = modal.querySelector('form');

        form.reset();
        form.elements['aksi'].value = mode;
        form.elements['id'].value   = data ? data.id : '';
        modal.querySelector('h3').textContent = (mode === 'edit' ? 'Edit ' : 'Tambah ') + cfg.nama;

        if (data) {
            Object.keys(data).forEach(function (kunci) {
                if (kunci === 'id' || kunci === 'aksi' || kunci === 'csrf' || kunci === 'entitas') return;
                var kolom = form.elements[kunci];
                if (kolom) kolom.value = data[kunci];
            });
        }

        bukaModal(modal);
    }

    /* ==========================================================
       LIHAT (detail read-only)
       ========================================================== */
    var kolomDetail = {
        catatan: [
            ['judul', 'Judul catatan'],
            ['penulis', 'Penulis'],
            ['jurusan', 'Jurusan'],
            ['upvote', 'Upvote'],
            ['status', 'Status'],
            ['alasan_laporan', 'Alasan laporan'],
            ['isi', 'Isi catatan']
        ],
        pengguna: [
            ['nama', 'Nama'],
            ['nim', 'NIM'],
            ['jurusan', 'Jurusan'],
            ['status', 'Status'],
            ['jumlah_catatan', 'Jumlah catatan']
        ]
    };

    function huruf1Besar(teks) {
        teks = String(teks);
        return teks.charAt(0).toUpperCase() + teks.slice(1);
    }

    function tampilkanDetail(entitas, data) {
        var modal = document.getElementById('modalLihat');
        var isi   = document.getElementById('isiDetail');
        var judul = document.getElementById('judulModalLihat');

        judul.textContent = 'Detail ' + entitas;
        isi.innerHTML = '';

        kolomDetail[entitas].forEach(function (par) {
            var nilai = data[par[0]];
            if (nilai === undefined || nilai === null || nilai === '') return;
            if (par[0] === 'status') nilai = huruf1Besar(nilai);

            var dt = document.createElement('dt');
            var dd = document.createElement('dd');
            dt.textContent = par[1];
            dd.textContent = nilai; // textContent = aman dari XSS
            if (par[0] === 'isi') dd.className = 'detail-isi';
            isi.appendChild(dt);
            isi.appendChild(dd);
        });

        bukaModal(modal);
    }

    /* ==========================================================
       KONFIRMASI + KIRIM (hapus, blokir, buka blokir)
       ========================================================== */
    function kirimAksi(entitas, aksi, id) {
        var form = document.getElementById('formAksi');
        form.elements['entitas'].value = entitas;
        form.elements['aksi'].value    = aksi;
        form.elements['id'].value      = id;
        form.submit();
    }

    /* ==========================================================
       DROPDOWN AKSI
       ========================================================== */
    var menuTerbuka   = null;
    var tombolTerbuka = null;

    function tutupDropdown() {
        if (!menuTerbuka) return;
        menuTerbuka.classList.remove('tampil');
        tombolTerbuka.setAttribute('aria-expanded', 'false');
        menuTerbuka   = null;
        tombolTerbuka = null;
    }

    function bukaDropdown(tombol) {
        var menu = tombol.parentNode.querySelector('.dropdown-menu');
        var sudahTerbuka = (menu === menuTerbuka);

        tutupDropdown();
        if (sudahTerbuka) return; // klik lagi = tutup

        menu.classList.add('tampil'); // ditampilkan dulu supaya ukurannya bisa dihitung

        var r      = tombol.getBoundingClientRect();
        var lebar  = menu.offsetWidth;
        var tinggi = menu.offsetHeight;

        var kiri = r.right - lebar;              // rata kanan dengan tombol
        if (kiri < 8) kiri = 8;

        var atas = r.bottom + 6;                 // default: muncul di bawah tombol
        if (atas + tinggi > window.innerHeight - 8) {
            atas = r.top - tinggi - 6;           // kalau tidak muat, muncul di atas
        }
        if (atas < 8) atas = 8;

        menu.style.left = kiri + 'px';
        menu.style.top  = atas + 'px';

        tombol.setAttribute('aria-expanded', 'true');
        menuTerbuka   = menu;
        tombolTerbuka = tombol;
    }

    // Menu ditutup kalau halaman di-scroll atau ukuran layar berubah
    window.addEventListener('scroll', tutupDropdown, true);
    window.addEventListener('resize', tutupDropdown);

    /* ==========================================================
       SATU LISTENER UNTUK SEMUA TOMBOL (event delegation)
       ========================================================== */
    document.addEventListener('click', function (e) {
        var tombol;

        // Tombol "Aksi ▾" -> buka/tutup dropdown
        var pemicu = e.target.closest('[data-dropdown]');
        if (pemicu) {
            bukaDropdown(pemicu);
            return;
        }
        // Klik di tempat lain (termasuk pilihan di menu) -> tutup dropdown dulu
        tutupDropdown();

        if ((tombol = e.target.closest('[data-tambah]'))) {
            siapkanForm(tombol.dataset.entitas, 'tambah', null);
            return;
        }

        if ((tombol = e.target.closest('[data-edit]'))) {
            siapkanForm(tombol.dataset.entitas, 'edit', JSON.parse(tombol.dataset.item));
            return;
        }

        if ((tombol = e.target.closest('[data-lihat]'))) {
            tampilkanDetail(tombol.dataset.entitas, JSON.parse(tombol.dataset.item));
            return;
        }

        if ((tombol = e.target.closest('[data-konfirmasi]'))) {
            if (window.confirm(tombol.dataset.pesan)) {
                kirimAksi(tombol.dataset.entitas, tombol.dataset.aksi, tombol.dataset.id);
            }
            return;
        }

        if (e.target.closest('[data-tutup]')) {
            tutupSemuaModal();
            return;
        }

        if (e.target.closest('[data-tutup-flash]')) {
            var pesan = document.getElementById('flashPesan');
            if (pesan) pesan.remove();
        }
    });

    /* ==========================================================
       PESAN SUKSES / ERROR: hilang otomatis
       ========================================================== */
    var flashPesan = document.getElementById('flashPesan');
    if (flashPesan) {
        setTimeout(function () {
            flashPesan.style.opacity = '0';
            setTimeout(function () {
                if (flashPesan.parentNode) flashPesan.remove();
            }, 300);
        }, 4000);
    }
})();