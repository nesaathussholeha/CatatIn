<?php

require_once APP_PATH . '/Models/Pengingat.php';

class PengingatController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // PROSES TAMBAH TUGAS
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $aksi = $_POST['aksi'] ?? '';

            if ($aksi === 'tambah') {
                $judul = trim($_POST['judul'] ?? '');
                $deadline = $_POST['deadline'] ?? '';

                if ($judul !== '' && $deadline !== '') {
                    Pengingat::tambah($judul, $deadline);
                }
            }

            // PROSES EDIT TUGAS
            elseif ($aksi === 'edit') {
                $id = (int) ($_POST['id'] ?? 0);
                $judul = trim($_POST['judul'] ?? '');
                $deadline = $_POST['deadline'] ?? '';

                if ($id > 0 && $judul !== '' && $deadline !== '') {
                    Pengingat::edit($id, $judul, $deadline);
                }
            }

            // PROSES HAPUS TUGAS
            elseif ($aksi === 'hapus') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id > 0) {
                    Pengingat::hapus($id);
                }
            }

            // PROSES UBAH STATUS
            elseif ($aksi === 'toggle') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id > 0) {
                    Pengingat::ubahStatus($id);
                }
            }

            header('Location: ' . BASE_URL . '/?page=pengingat');
            exit;
        }

        // AMBIL DATA DARI MODEL
        $tugas = Pengingat::semua();

        // FILTER STATUS
        $filter = $_GET['filter'] ?? 'semua';

        if ($filter === 'belum') {
            $tugas = array_filter(
                $tugas,
                fn($item) => !$item['selesai']
            );
        } elseif ($filter === 'selesai') {
            $tugas = array_filter(
                $tugas,
                fn($item) => $item['selesai']
            );
        }

        // PENGURUTAN DEADLINE
        $urut = $_GET['urut'] ?? 'terdekat';

        usort($tugas, function ($a, $b) use ($urut) {
            if ($urut === 'terjauh') {
                return strcmp($b['deadline'], $a['deadline']);
            }

            return strcmp($a['deadline'], $b['deadline']);
        });

        // DATA UNTUK LAYOUT
        $page_title = 'Pengingat';
        $active_menu = 'tugas';
        $page_css = 'pengingat.css';
        $page_js = 'pengingat.js';

        // DATA USER
        // Sesuaikan dengan sistem login CatatIn yang sudah ada.
        $user_name = $_SESSION['user_name'] ?? 'Username';
        $user_role = $_SESSION['user_role'] ?? 'User';

        // SIAPKAN VIEW
        ob_start();
        require VIEW_PATH . '/user/pengingat.php';
        $content = ob_get_clean();

        // TAMPILKAN DENGAN LAYOUT UTAMA
        require VIEW_PATH . '/layouts/main.php';
    }
}