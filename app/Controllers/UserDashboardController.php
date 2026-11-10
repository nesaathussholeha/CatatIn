
<?php

class UserDashboardController
{
    public function index()
    {
        // 1. Periksa apakah pengguna sudah login
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // 2. Periksa hak akses
        $role = strtolower($_SESSION['role'] ?? '');

        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/?page=admin-dashboard');
            exit;
        }

        // 3. Data layout
        $page_title = 'Jelajahi Catatan';
        $user_name = $_SESSION['nama_lengkap'] ?? 'User';
        $user_role = $_SESSION['role'] ?? 'user';
        $active_menu = 'dashboard';

        $page_css = 'dashboard_user.css';
        $page_js = 'dashboard_user.js';

        // 4. Data katalog sementara
        $catatan = [
            [
                'id' => 1,
                'judul' => 'Rangkuman Struktur Data — Pohon & Graf',
                'matkul' => 'Struktur Data',
                'jurusan' => 'Informatika',
                'kategori' => 'Rangkuman',
                'penulis' => 'Nazma F.',
                'upvote' => 128,
                'tanggal' => '2026-09-25'
            ],
            [
                'id' => 2,
                'judul' => 'Kumpulan Soal UAS Basis Data 2025',
                'matkul' => 'Basis Data',
                'jurusan' => 'Sistem Informasi',
                'kategori' => 'Soal Ujian',
                'penulis' => 'sara wijayanto',
                'upvote' => 96,
                'tanggal' => '2026-09-22'
            ],
            [
                'id' => 3,
                'judul' => 'Modul Praktikum Rangkaian Digital',
                'matkul' => 'Rangkaian Digital',
                'jurusan' => 'Elektro',
                'kategori' => 'Modul',
                'penulis' => 'Nadia omara',
                'upvote' => 54,
                'tanggal' => '2026-09-20'
            ],
            [
                'id' => 4,
                'judul' => 'Rangkuman Pemrograman Web — Sesi 1–7',
                'matkul' => 'Pemrograman Web',
                'jurusan' => 'Informatika',
                'kategori' => 'Rangkuman',
                'penulis' => 'Afifatul M.',
                'upvote' => 41,
                'tanggal' => '2026-09-27'
            ],
            [
                'id' => 5,
                'judul' => 'Rangkuman Struktur Data',
                'matkul' => 'Struktur Data',
                'jurusan' => 'Informatika',
                'kategori' => 'Rangkuman',
                'penulis' => 'Rizky A.',
                'upvote' => 37,
                'tanggal' => '2026-09-10'
            ],
            [
                'id' => 6,
                'judul' => 'Modul Praktikum HTML/CSS',
                'matkul' => 'Pemrograman Web',
                'jurusan' => 'Informatika',
                'kategori' => 'Modul',
                'penulis' => 'Dimas P.',
                'upvote' => 63,
                'tanggal' => '2026-09-12'
            ]
        ];

        // 5. Urutkan catatan berdasarkan tanggal terbaru
        usort($catatan, function ($a, $b) {
            return strcmp($b['tanggal'], $a['tanggal']);
        });

        // 6. Data filter
        $daftarJurusan = [
            'Informatika',
            'Elektro',
            'Sistem Informasi'
        ];

        $daftarKategori = [
            'Rangkuman',
            'Soal Ujian',
            'Modul'
        ];

        // 7. Render view dashboard
        ob_start();

        require VIEW_PATH . '/user/dashboard.php';

        $content = ob_get_clean();

        // 8. Gunakan layout utama
        require VIEW_PATH . '/layouts/main.php';
    }
}
