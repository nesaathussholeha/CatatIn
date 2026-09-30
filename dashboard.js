/* dashboard.js — interaksi halaman Dashboard (pencarian, filter, urutan, upvote) */
(function () {
    'use strict';

    var grid     = document.getElementById('grid');
    var kartu    = Array.prototype.slice.call(grid.querySelectorAll('.kartu'));
    var cari     = document.getElementById('cari');
    var jurusan  = document.getElementById('jurusan');
    var urut     = document.getElementById('urut');
    var kosong   = document.getElementById('kosong');
    var chips    = document.querySelectorAll('.chip[data-kategori]');
    var kategoriAktif = '';

    /* ---------- Toast ---------- */
    function tampilToast(pesan) {
        var el = document.createElement('div');
        el.className = 'toast';
        el.setAttribute('role', 'status');
        el.textContent = pesan;
        document.body.appendChild(el);
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, 3000);
    }


    /* ---------- Filter + urutan ---------- */
    function jumlahUpvote(k) {
        var t = k.querySelector('.upvote');
        return Number(t.dataset.dasar) + (t.classList.contains('aktif') ? 1 : 0);
    }

    function terapkan() {
        var kata = cari.value.trim().toLowerCase();
        var tampil = 0;

        kartu.forEach(function (k) {
            var cocok =
                (jurusan.value === '' || k.dataset.jurusan === jurusan.value) &&
                (kategoriAktif === '' || k.dataset.kategori === kategoriAktif) &&
                (kata === '' || k.dataset.cari.indexOf(kata) !== -1);
            k.hidden = !cocok;
            if (cocok) { tampil++; }
        });

        kartu.slice().sort(function (a, b) {
            return urut.value === 'populer'
                ? jumlahUpvote(b) - jumlahUpvote(a)
                : b.dataset.tanggal.localeCompare(a.dataset.tanggal);
        }).forEach(function (k) { grid.appendChild(k); });

        kosong.hidden = tampil > 0;
        grid.hidden = tampil === 0;
    }

    cari.addEventListener('input', terapkan);
    jurusan.addEventListener('change', terapkan);
    urut.addEventListener('change', terapkan);
    document.getElementById('form-cari').addEventListener('submit', function (e) { e.preventDefault(); });

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            var nyala = chip.getAttribute('aria-pressed') !== 'true';
            chips.forEach(function (c) {
                c.classList.remove('aktif');
                c.setAttribute('aria-pressed', 'false');
            });
            if (nyala) {
                chip.classList.add('aktif');
                chip.setAttribute('aria-pressed', 'true');
            }
            kategoriAktif = nyala ? chip.dataset.kategori : '';
            terapkan();
        });
    });

    document.getElementById('reset').addEventListener('click', function () {
        cari.value = '';
        jurusan.value = '';
        kategoriAktif = '';
        chips.forEach(function (c) {
            c.classList.remove('aktif');
            c.setAttribute('aria-pressed', 'false');
        });
        terapkan();
    });

    grid.addEventListener('click', function (e) {
        var tombol = e.target.closest('.upvote');
        if (!tombol) { return; }
        var aktif = tombol.classList.toggle('aktif');
        tombol.setAttribute('aria-pressed', String(aktif));
        tombol.querySelector('.jumlah').textContent = Number(tombol.dataset.dasar) + (aktif ? 1 : 0);
    });
})();
