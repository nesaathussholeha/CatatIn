document.addEventListener('DOMContentLoaded', () => {
    // 1. Toggle Sidebar untuk Layar Kecil
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar && sidebarOverlay) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('tampil');
            sidebarOverlay.classList.toggle('tampil');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('tampil');
            sidebarOverlay.classList.remove('tampil');
        });
    }

    // 2. Tutup Pesan Flash
    document.querySelectorAll('[data-tutup-flash]').forEach(btn => {
        btn.addEventListener('click', () => {
            const flash = btn.closest('.flash');
            if (flash) flash.remove();
        });
    });

    // 3. Kontrol Dropdown Menu Tabel
    document.querySelectorAll('[data-dropdown]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const parent = btn.closest('.dropdown');
            const menu = parent.querySelector('.dropdown-menu');

            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.remove('tampil');
            });

            menu.classList.toggle('tampil');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('tampil'));
    });

    // 4. Modal Detail (Lihat Data)
    const modalLihat = document.getElementById('modalLihat');
    const isiDetail = document.getElementById('isiDetail');

    document.querySelectorAll('[data-lihat]').forEach(btn => {
        btn.addEventListener('click', () => {
            const data = JSON.parse(btn.dataset.item);
            const entitas = btn.dataset.entitas;

            let html = '';
            if (entitas === 'catatan') {
                html = `
                    <dt>Judul Catatan</dt><dd>${e(data.judul)}</dd>
                    <dt>Penulis</dt><dd>${e(data.penulis)} (${e(data.jurusan)})</dd>
                    <dt>Jumlah Upvote</dt><dd>${data.upvote}</dd>
                    <dt>Status</dt><dd>${e(data.status)}</dd>
                    ${data.alasan_laporan ? `<dt>Alasan Laporan</dt><dd>${e(data.alasan_laporan)}</dd>` : ''}
                    <dt>Isi Catatan</dt><dd style="white-space: pre-wrap;">${e(data.isi)}</dd>
                `;
            } else if (entitas === 'pengguna') {
                html = `
                    <dt>Nama Pengguna</dt><dd>${e(data.nama)}</dd>
                    <dt>NIM</dt><dd>${e(data.nim)}</dd>
                    <dt>Jurusan</dt><dd>${e(data.jurusan)}</dd>
                    <dt>Status Akun</dt><dd>${e(data.status)}</dd>
                    <dt>Total Catatan Ditulis</dt><dd>${data.jumlah_catatan ?? 0} Catatan</dd>
                `;
            }

            if (isiDetail) isiDetail.innerHTML = html;
            if (modalLihat) modalLihat.classList.add('tampil');
        });
    });

    // 5. Konfirmasi Aksi (Hapus/Blokir/Terbitkan)
    const formAksi = document.getElementById('formAksi');

    document.querySelectorAll('[data-konfirmasi]').forEach(btn => {
        btn.addEventListener('click', () => {
            const pesan = btn.dataset.pesan || 'Apakah Anda yakin ingin melanjutkan aksi ini?';
            if (confirm(pesan)) {
                formAksi.querySelector('input[name="entitas"]').value = btn.dataset.entitas;
                formAksi.querySelector('input[name="aksi"]').value = btn.dataset.aksi;
                formAksi.querySelector('input[name="id"]').value = btn.dataset.id;
                formAksi.submit();
            }
        });
    });

    // 6. Tombol Tutup Modal
    document.querySelectorAll('[data-tutup]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.modal-latar').forEach(m => m.classList.remove('tampil'));
        });
    });

    // Helper Escape String
    function e(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});