
function initKelolaPengguna() {
    const tabel = document.getElementById('daftar-laporan');
    const cari = document.getElementById('cari-laporan');
    const filterStatus = document.getElementById('filter-status');
    const tabs = document.querySelectorAll('.kp-tab');
    const kosong = document.getElementById('laporan-kosong');
    const jumlahLaporan = document.getElementById('jumlah-laporan');
    const toast = document.getElementById('kp-toast');

    const detailId = document.getElementById('detail-id');
    const detailStatus = document.getElementById('detail-status');
    const detailMeta = document.getElementById('detail-meta');
    const detailIsi = document.getElementById('detail-isi');
    const detailBukti = document.getElementById('detail-bukti');
    const detailPengguna = document.getElementById('detail-pengguna');
    const detailJumlah = document.getElementById('detail-jumlah');

    const tombolTolak = document.getElementById('tolak-laporan');
    const tombolTinjau = document.getElementById('tinjau-bukti');

    if (
        !tabel || !cari || !filterStatus || !kosong ||
        !jumlahLaporan || !toast || !tombolTolak || !tombolTinjau
    ) {
        return;
    }

    let tabAktif = 'semua';
    let laporanTerpilih = null;

    // Menampilkan notifikasi
    function tampilkanToast(pesan) {
        toast.textContent = pesan;
        toast.classList.add('tampil');

        setTimeout(function () {
            toast.classList.remove('tampil');
        }, 2500);
    }

    // Mengubah tombol berdasarkan status akun
    function perbaruiTombol(row) {
        const kolomTindakan = row.querySelector('.kp-actions');
        const statusAkun = row.dataset.statusAkun;

        let tombol = '';

        if (statusAkun === 'aktif') {
            tombol = `
                <button type="button"
                    class="kp-btn nonaktifkan"
                    data-action="nonaktifkan">
                    Nonaktifkan
                </button>

                <button type="button"
                    class="kp-btn blokir"
                    data-action="blokir">
                    Blokir
                </button>
            `;
        } else if (statusAkun === 'nonaktif') {
            tombol = `
                <button type="button"
                    class="kp-btn aktifkan"
                    data-action="aktifkan">
                    Aktifkan
                </button>

                <button type="button"
                    class="kp-btn blokir"
                    data-action="blokir">
                    Blokir
                </button>
            `;
        } else if (statusAkun === 'diblokir') {
            tombol = `
                <button type="button"
                    class="kp-btn buka-blokir"
                    data-action="buka-blokir">
                    Buka Blokir
                </button>
            `;
        }

        kolomTindakan.innerHTML = tombol + `
            <button type="button"
                class="kp-link"
                data-action="detail">
                Detail
            </button>
        `;
    }

    // Menampilkan detail laporan
    function tampilkanDetail(row) {
        if (!row) return;

        laporanTerpilih = row;

        document.querySelectorAll('.kp-row').forEach(function (item) {
            item.classList.remove('terpilih');
        });

        row.classList.add('terpilih');

        const data = row.dataset;

        detailId.textContent = data.id;

        detailStatus.textContent =
            data.status === 'Menunggu'
                ? 'Menunggu Peninjauan'
                : 'Selesai';

        detailStatus.className =
            'kp-status ' + data.status.toLowerCase();

        detailMeta.textContent =
            data.pelapor + ' melaporkan ' + data.terlapor +
            ' · ' + data.tanggal + ', ' + data.waktu;

        detailIsi.textContent = data.isi;

        detailBukti.textContent =
            'Bukti terlampir: ' + data.bukti;

        let statusAkun = data.statusAkun;

        if (statusAkun === 'aktif') {
            statusAkun = 'Aktif';
        } else if (statusAkun === 'nonaktif') {
            statusAkun = 'Dinonaktifkan';
        } else if (statusAkun === 'diblokir') {
            statusAkun = 'Diblokir';
        }

        detailPengguna.textContent =
            data.terlapor + ' · ' + statusAkun;

        detailJumlah.textContent =
            data.jumlah + ' laporan terkait akun ini';
    }

    // Filter laporan
    function filterLaporan() {
        const kataKunci = cari.value.toLowerCase().trim();
        const statusDipilih = filterStatus.value;
        const semuaRow = tabel.querySelectorAll('.kp-row');

        let jumlahTampil = 0;

        semuaRow.forEach(function (row) {
            const cocokCari =
                row.dataset.cari.includes(kataKunci);

            const cocokTab =
                tabAktif === 'semua' ||
                row.dataset.status.toLowerCase() === tabAktif;

            const cocokStatus =
                statusDipilih === '' ||
                row.dataset.status === statusDipilih;

            const tampil =
                cocokCari && cocokTab && cocokStatus;

            row.hidden = !tampil;

            if (tampil) {
                jumlahTampil++;
            }
        });

        kosong.hidden = jumlahTampil > 0;

        jumlahLaporan.textContent =
            'Menampilkan ' + jumlahTampil +
            ' dari ' + semuaRow.length + ' laporan';

        if (laporanTerpilih && laporanTerpilih.hidden) {
            const rowPertama = Array.from(semuaRow).find(function (row) {
                return !row.hidden;
            });

            if (rowPertama) {
                tampilkanDetail(rowPertama);
            } else {
                laporanTerpilih.classList.remove('terpilih');
                laporanTerpilih = null;
            }
        }
    }

    // Memilih tab laporan
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabAktif = tab.dataset.tab;

            tabs.forEach(function (item) {
                item.classList.remove('aktif');
            });

            tab.classList.add('aktif');

            filterLaporan();
        });
    });

    // Pencarian dan filter status
    cari.addEventListener('input', filterLaporan);
    filterStatus.addEventListener('change', filterLaporan);

    // Klik baris dan tombol tindakan
    tabel.addEventListener('click', function (event) {
        const row = event.target.closest('.kp-row');

        if (!row) return;

        const tombol = event.target.closest('[data-action]');

        if (!tombol) {
            tampilkanDetail(row);
            return;
        }

        const aksi = tombol.dataset.action;

        tampilkanDetail(row);

        // Detail
        if (aksi === 'detail') {
            document.getElementById('detail-laporan').scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });

            return;
        }

        // Nonaktifkan akun
        if (aksi === 'nonaktifkan') {
            if (!confirm(
                'Nonaktifkan sementara akun ' + row.dataset.terlapor + '?'
            )) return;

            row.dataset.statusAkun = 'nonaktif';

            perbaruiTombol(row);
            tampilkanDetail(row);

            tampilkanToast('Akun berhasil dinonaktifkan.');
        }

        // Aktifkan kembali akun
        else if (aksi === 'aktifkan') {
            if (!confirm(
                'Aktifkan kembali akun ' + row.dataset.terlapor + '?'
            )) return;

            row.dataset.statusAkun = 'aktif';

            perbaruiTombol(row);
            tampilkanDetail(row);

            tampilkanToast('Akun berhasil diaktifkan kembali.');
        }

        // Blokir akun
        else if (aksi === 'blokir') {
            if (!confirm(
                'Blokir akun ' + row.dataset.terlapor + '?'
            )) return;

            row.dataset.statusAkun = 'diblokir';

            perbaruiTombol(row);
            tampilkanDetail(row);

            tampilkanToast('Akun berhasil diblokir.');
        }

        // Buka blokir akun
        else if (aksi === 'buka-blokir') {
            if (!confirm(
                'Buka blokir akun ' + row.dataset.terlapor + '?'
            )) return;

            row.dataset.statusAkun = 'aktif';

            perbaruiTombol(row);
            tampilkanDetail(row);

            tampilkanToast('Blokir akun berhasil dibuka.');
        }
    });

    // Menolak atau menyelesaikan laporan
    tombolTolak.addEventListener('click', function () {
        if (!laporanTerpilih) return;

        if (laporanTerpilih.dataset.status === 'Selesai') {
            tampilkanToast('Laporan ini sudah selesai.');
            return;
        }

        if (!confirm('Tandai laporan ini sebagai selesai?')) {
            return;
        }

        laporanTerpilih.dataset.status = 'Selesai';

        const status = laporanTerpilih.querySelector('.kp-status');

        status.textContent = 'Selesai';
        status.className = 'kp-status selesai';

        // Perbarui jumlah tab
        const jumlahMenunggu = Array.from(
            tabel.querySelectorAll('.kp-row')
        ).filter(function (row) {
            return row.dataset.status === 'Menunggu';
        }).length;

        const jumlahSelesai = Array.from(
            tabel.querySelectorAll('.kp-row')
        ).filter(function (row) {
            return row.dataset.status === 'Selesai';
        }).length;

        tabs.forEach(function (tab) {
            if (tab.dataset.tab === 'menunggu') {
                tab.textContent = 'Menunggu Tinjau (' + jumlahMenunggu + ')';
            }

            if (tab.dataset.tab === 'selesai') {
                tab.textContent = 'Selesai (' + jumlahSelesai + ')';
            }
        });

        tampilkanDetail(laporanTerpilih);
        filterLaporan();

        tampilkanToast('Laporan berhasil diselesaikan.');
    });

    // Tinjau bukti
    tombolTinjau.addEventListener('click', function () {
        if (!laporanTerpilih) return;

        tampilkanToast('Belum ada file bukti yang terhubung.');
    });

    // Tampilkan laporan pertama
    const laporanPertama = tabel.querySelector('.kp-row');

    if (laporanPertama) {
        tampilkanDetail(laporanPertama);
    }

    filterLaporan();
}

document.addEventListener('DOMContentLoaded', initKelolaPengguna);
