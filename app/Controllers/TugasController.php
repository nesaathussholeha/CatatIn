<?php

require_once ROOT_PATH . '/app/Models/Tugas.php';

class TugasController
{
    public function index()
    {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Harus login
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // PROSES FORM (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $aksi = $_POST['aksi'] ?? '';

            // TAMBAH
            if ($aksi === 'tambah') {
                $judul    = trim($_POST['judul'] ?? '');
                $deadline = $_POST['deadline'] ?? '';

                if ($judul !== '' && $deadline !== '') {
                    Tugas::tambah($judul, $deadline);
                }
            }

            // EDIT
            elseif ($aksi === 'edit') {
                $id       = (int) ($_POST['id'] ?? 0);
                $judul    = trim($_POST['judul'] ?? '');
                $deadline = $_POST['deadline'] ?? '';

                if ($id > 0 && $judul !== '' && $deadline !== '') {
                    Tugas::edit($id, $judul, $deadline);
                }
            }

            // HAPUS
            elseif ($aksi === 'hapus') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id > 0) {
                    Tugas::hapus($id);
                }
            }

            // UBAH STATUS
            elseif ($aksi === 'toggle') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id > 0) {
                    Tugas::ubahStatus($id);
                }
            }

            header('Location: ' . BASE_URL . '/?page=tugas');
            exit;
        }

        // AMBIL DATA
        $tugas = Tugas::semua();

        // FILTER STATUS
        $filter = $_GET['filter'] ?? 'semua';

        if ($filter === 'belum') {
            $tugas = array_filter($tugas, fn($item) => !$item['selesai']);
        } elseif ($filter === 'selesai') {
            $tugas = array_filter($tugas, fn($item) => $item['selesai']);
        }

        // URUTKAN DEADLINE
        $urut = $_GET['urut'] ?? 'terdekat';

        usort($tugas, function ($a, $b) use ($urut) {
            if ($urut === 'terjauh') {
                return strcmp($b['deadline'], $a['deadline']);
            }

            return strcmp($a['deadline'], $b['deadline']);
        });

        // DATA UNTUK LAYOUT
        $page_title  = 'Tugas';
        $active_menu = 'tugas';
        $page_css    = 'tugas.css';
        $page_js     = 'tugas.js';

        // DATA USER (disamakan dengan key session di router)
        $user_name = $_SESSION['user_name'] ?? 'Username';
        $user_role = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? 'User');

        // SIAPKAN VIEW
        ob_start();
        require VIEW_PATH . '/user/tugas.php';
        $content = ob_get_clean();

        // TAMPILKAN DENGAN LAYOUT UTAMA
        require VIEW_PATH . '/layouts/main.php';
    }
}