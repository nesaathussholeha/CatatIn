
function tampilkanToast(pesan) {
    let toast = document.querySelector('.dashboard-user .toast');

    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast';
        document.querySelector('.dashboard-user').appendChild(toast);
    }

    toast.textContent = pesan;
    toast.classList.add('tampil');

    setTimeout(function () {
        toast.classList.remove('tampil');
    }, 2500);
}

function initDashboard() {
    const grid = document.getElementById('grid');
    const cari = document.getElementById('cari');
    const jurusan = document.getElementById('jurusan');
    const urut = document.getElementById('urut');
    const kosong = document.getElementById('kosong');
    const formCari = document.getElementById('form-cari');
    const tombolReset = document.getElementById('reset');

    if (!grid || !cari || !jurusan || !urut || !kosong || !formCari || !tombolReset) {
        return;
    }

    let kategoriAktif = 'semua';

    const semuaChipKategori = document.querySelectorAll('[data-kategori]');

    function filterCatatan() {
        const kataKunci = cari.value.toLowerCase().trim();
        const jurusanDipilih = jurusan.value;
        const kartu = grid.querySelectorAll('.kartu');

        let jumlahTampil = 0;

        kartu.forEach(function (item) {
            const teksCari = (item.dataset.cari || '').toLowerCase();
            const jurusanItem = item.dataset.jurusan;
            const kategoriItem = item.dataset.kategori;

            const cocokCari = teksCari.includes(kataKunci);

            const cocokJurusan =
                jurusanDipilih === '' ||
                jurusanItem === jurusanDipilih;

            const cocokKategori =
                kategoriAktif === 'semua' ||
                kategoriItem === kategoriAktif;

            const tampil = cocokCari && cocokJurusan && cocokKategori;

            item.style.display = tampil ? '' : 'none';

            if (tampil) {
                jumlahTampil++;
            }
        });

        kosong.hidden = jumlahTampil > 0;
    }

    function urutkanCatatan() {
        const kartu = Array.from(grid.querySelectorAll('.kartu'));

        kartu.sort(function (a, b) {
            if (urut.value === 'populer') {
                return Number(b.dataset.dasar || 0) - Number(a.dataset.dasar || 0);
            }

            return new Date(b.dataset.tanggal) - new Date(a.dataset.tanggal);
        });

        kartu.forEach(function (item) {
            grid.appendChild(item);
        });

        filterCatatan();
    }

    cari.addEventListener('input', filterCatatan);

    jurusan.addEventListener('change', filterCatatan);

    urut.addEventListener('change', urutkanCatatan);

    formCari.addEventListener('submit', function (event) {
        event.preventDefault();
        filterCatatan();
    });

    tombolReset.addEventListener('click', function () {
        cari.value = '';
        jurusan.value = '';
        urut.value = 'terbaru';
        kategoriAktif = 'semua';

        semuaChipKategori.forEach(function (chip) {
            const aktif = chip.dataset.kategori === 'semua';

            chip.classList.toggle('aktif', aktif);
            chip.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        });

        urutkanCatatan();
    });

    semuaChipKategori.forEach(function (chip) {
        chip.addEventListener('click', function () {
            kategoriAktif = chip.dataset.kategori;

            semuaChipKategori.forEach(function (item) {
                const aktif = item === chip;

                item.classList.toggle('aktif', aktif);
                item.setAttribute('aria-pressed', aktif ? 'true' : 'false');
            });

            filterCatatan();
        });
    });

    grid.addEventListener('click', function (event) {
        const tombol = event.target.closest('.upvote');

        if (!tombol) {
            return;
        }

        const jumlah = tombol.querySelector('.jumlah');

        if (!jumlah) {
            return;
        }

        let nilai = Number(jumlah.textContent);

        nilai++;
        jumlah.textContent = nilai;

        tampilkanToast('Upvote berhasil ditambahkan!');
    });

    filterCatatan();
}

document.addEventListener('DOMContentLoaded', initDashboard);
