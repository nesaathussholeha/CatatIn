<?php

class ProfilePenulisController
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }


        // Data sementara
        $penulis = [
            'nama' => 'Nama Lengkap',
            'username' => '@namapenulis',
            'foto' => '',
            'jurusan' => 'Mahasiswa Informatika',
            'angkatan' => '2025',
            'bergabung' => 'September 2026',
            'jumlah_catatan' => 6,
            'jumlah_upvote' => 486,
            'jumlah_unduhan' => 1240,
            'jumlah_matkul' => 4
        ];

        $catatan = [
            [
                'id' => 1,
                'judul' => 'Rangkuman Struktur Data',
                'kategori' => 'Struktur Data',
                'tipe' => 'Rangkuman',
                'deskripsi' => 'Konsep array, linked list, stack, dan queue.',
                'upvote' => 128,
                'tanggal' => '2026-10-08',
                'jumlah_halaman' => 7,
                'ukuran' => '1.2 MB'
            ],
            [
                'id' => 1,
                'judul' => 'Rangkuman Struktur Data',
                'kategori' => 'Struktur Data',
                'tipe' => 'Rangkuman',
                'deskripsi' => 'Konsep array, linked list, stack, dan queue.',
                'upvote' => 128,
                'tanggal' => '2026-10-08',
                'jumlah_halaman' => 7,
                'ukuran' => '1.2 MB'
            ]
        ];

        $page_title = 'Profil Penulis';
        $user_name = $_SESSION['nama_lengkap'] ?? 'Username';
        $user_role = $_SESSION['role'] ?? 'User';
        $active_menu = 'dashboard';
        $page_css = 'profile_penulis.css';
        $page_js = 'profile_penulis.js';

        ob_start();
        require VIEW_PATH . '/user/profile_penulis.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}
