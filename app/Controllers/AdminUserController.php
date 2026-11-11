
<?php

class AdminUserController
{
    public function index()
    {
        // 1. Periksa login
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // 2. Periksa hak akses
        $role = strtolower($_SESSION['role'] ?? '');

        if ($role !== 'admin') {
            header('Location: ' . BASE_URL . '/?page=dashboard');
            exit;
        }

        // 3. Data laporan sementara
        $daftarLaporan = [
            [
                'id' => 'LPR-001',
                'pelapor' => 'Aulia Putri',
                'username_pelapor' => '@auliaputri',
                'terlapor' => 'Bima Sakti',
                'username_terlapor' => '@bimasakti',
                'alasan' => 'Spam berulang',
                'jumlah_laporan' => 3,
                'status' => 'Menunggu',
                'status_akun' => 'aktif',
                'tanggal' => '06 Nov 2026',
                'waktu' => '09.24',
                'isi' => 'Pengguna ini mengirim promosi berulang di kolom komentar catatan dan mengganggu diskusi. Mohon ditinjau oleh admin.',
                'bukti' => '2 tangkapan layar'
            ],
            [
                'id' => 'LPR-002',
                'pelapor' => 'Dinda Sari',
                'username_pelapor' => '@dindasari',
                'terlapor' => 'Raka Pratama',
                'username_terlapor' => '@rakapratama',
                'alasan' => 'Pelecehan',
                'jumlah_laporan' => 2,
                'status' => 'Menunggu',
                'status_akun' => 'aktif',
                'tanggal' => '05 Nov 2026',
                'waktu' => '14.10',
                'isi' => 'Pengguna memberikan komentar yang tidak pantas dan mengganggu kenyamanan pengguna lain.',
                'bukti' => '1 tangkapan layar'
            ],
            [
                'id' => 'LPR-003',
                'pelapor' => 'Fajar Adi',
                'username_pelapor' => '@fajaradi',
                'terlapor' => 'Nadia Putri',
                'username_terlapor' => '@nadiaputri',
                'alasan' => 'Akun palsu',
                'jumlah_laporan' => 1,
                'status' => 'Selesai',
                'status_akun' => 'diblokir',
                'tanggal' => '04 Nov 2026',
                'waktu' => '11.35',
                'isi' => 'Pengguna diduga menggunakan identitas orang lain untuk membuat akun.',
                'bukti' => '3 tangkapan layar'
            ]
        ];

        // 4. Hitung jumlah laporan
        $totalLaporan = count($daftarLaporan);
        $menunggu = 0;
        $selesai = 0;

        foreach ($daftarLaporan as $laporan) {
            if ($laporan['status'] === 'Menunggu') {
                $menunggu++;
            } elseif ($laporan['status'] === 'Selesai') {
                $selesai++;
            }
        }

        // 5. Data layout
        $page_title = 'Kelola Pengguna';
        $user_name = $_SESSION['nama_lengkap'] ?? 'Administrator';
        $user_role = $_SESSION['role'] ?? 'admin';
        $is_admin = true;
        $active_menu = 'kelola_pengguna';

        $page_css = 'kelola_pengguna.css';
        $page_js = 'kelola_pengguna.js';

        // 6. Render view
        ob_start();

        require VIEW_PATH . '/admin/kelola_pengguna.php';

        $content = ob_get_clean();

        // 7. Gunakan layout utama
        require VIEW_PATH . '/layouts/main.php';
    }
}
