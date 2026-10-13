/* dashboard.js — khusus halaman dashboard.php (pencarian/filter/urutan/upvote).
   Menu sidebar mobile ditangani oleh ../layout_user/script.js */
(function () {
    'use strict';

    /* ==========================================================
       UTILITAS: TOAST
       ========================================================== */
    function tampilToast(pesan, galat) {
        var el = document.createElement('div');
        el.className = 'toast' + (galat ? ' toast-error' : '');
        el.setAttribute('role', 'status');
        el.textContent = pesan;
        document.body.appendChild(el);
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, 3000);
    }

    /* ==========================================================
       DASHBOARD (pencarian, filter, urutan, upvote)
       ========================================================== */
    function initDashboard() {
        var grid = document.getElementById('grid');
        if (!grid) { return; }

        var kartu   = Array.prototype.slice.call(grid.querySelectorAll('.kartu'));
        var cari    = document.getElementById('cari');
        var jurusan = document.getElementById('jurusan');
        var urut    = document.getElementById('urut');
        var kosong  = document.getElementById('kosong');
        var formCari = document.getElementById('form-cari');
        var tombolReset = document.getElementById('reset');
        var chips   = document.querySelectorAll('.chip[data-kategori]');
        var kategoriAktif = '';

        function jumlahUpvote(k) {
            var t = k.querySelector('.upvote');
            return Number(t.dataset.dasar) + (t.classList.contains('aktif') ? 1 : 0);
        }

        function terapkan() {
            var kata = cari.value.trim().toLowerCase();
            var tampil = 0;

            // Tahap 1: filter
            kartu.forEach(function (k) {
                var cocok =
                    (jurusan.value === '' || k.dataset.jurusan === jurusan.value) &&
                    (kategoriAktif === '' || k.dataset.kategori === kategoriAktif) &&
                    (kata === '' || k.dataset.cari.indexOf(kata) !== -1);
                k.hidden = !cocok;
                if (cocok) { tampil++; }
            });

            // Tahap 2: urutkan (pada salinan array)
            kartu.slice().sort(function (a, b) {
                if (urut.value === 'populer') {
                    return jumlahUpvote(b) - jumlahUpvote(a);
                }
                return b.dataset.tanggal.localeCompare(a.dataset.tanggal);
            }).forEach(function (k) { grid.appendChild(k); });

            // Tahap 3: pesan kosong
            kosong.hidden = tampil > 0;
            grid.hidden = tampil === 0;
        }

        function matikanSemuaChip() {
            chips.forEach(function (c) {
                c.classList.remove('aktif');
                c.setAttribute('aria-pressed', 'false');
            });
        }

        // Pencarian, jurusan, urutan
        cari.addEventListener('input', terapkan);
        jurusan.addEventListener('change', terapkan);
        urut.addEventListener('change', terapkan);
        formCari.addEventListener('submit', function (e) { e.preventDefault(); });

        // Chip kategori (toggle, hanya satu aktif)
        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                var nyala = chip.getAttribute('aria-pressed') !== 'true';
                matikanSemuaChip();
                if (nyala) {
                    chip.classList.add('aktif');
                    chip.setAttribute('aria-pressed', 'true');
                }
                kategoriAktif = nyala ? chip.dataset.kategori : '';
                terapkan();
            });
        });

        // Reset
        tombolReset.addEventListener('click', function () {
            cari.value = '';
            jurusan.value = '';
            kategoriAktif = '';
            matikanSemuaChip();
            terapkan();
            cari.focus();
        });

        // Upvote (event delegation) — hanya sisi klien
        grid.addEventListener('click', function (e) {
            var tombol = e.target.closest('.upvote');
            if (!tombol) { return; }

            var aktif = tombol.classList.toggle('aktif');
            tombol.setAttribute('aria-pressed', String(aktif));
            tombol.querySelector('.jumlah').textContent =
                Number(tombol.dataset.dasar) + (aktif ? 1 : 0);

            // Jika sedang mode "Terpopuler", susun ulang otomatis
            if (urut.value === 'populer') { terapkan(); }

            tampilToast(aktif ? 'Upvote ditambahkan' : 'Upvote dibatalkan');
        });

        terapkan(); // sinkronkan tampilan awal
    }

    initDashboard();
})();