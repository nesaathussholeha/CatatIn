
<?php

class AdminDashboardController
{
    public function index()
    {
        // Pastikan hanya admin yang dapat mengakses dashboard.
        if (
            !isset($_SESSION['role']) ||
            strtolower($_SESSION['role']) !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // DATA AWAL
        if (!isset($_SESSION['catatan_data'])) {
            $_SESSION['catatan_data'] = [
                [
                    'id' => 1,
                    'judul' => 'Ringkasan Struktur Data — Binary Tree',
                    'penulis' => 'Sarah Faradila',
                    'jurusan' => 'Informatika',
                    'upvote' => 128,
                    'status' => 'Terbit',
                    'isi' => 'Catatan mengenai struktur data Binary Tree, jenis tree, traversal, dan penerapannya.',
                    'alasan' => ''
                ],
                [
                    'id' => 2,
                    'judul' => 'Rangkuman Basis Data Relasional',
                    'penulis' => 'Nazma Fairuz M.',
                    'jurusan' => 'Sistem Informasi',
                    'upvote' => 94,
                    'status' => 'Terbit',
                    'isi' => 'Rangkuman database relasional, tabel, primary key, foreign key, dan relasi.',
                    'alasan' => ''
                ],
                [
                    'id' => 3,
                    'judul' => 'Catatan Kalkulus II — Integral Lipat',
                    'penulis' => 'Bagas Wicaksono',
                    'jurusan' => 'Teknik Informatika',
                    'upvote' => 61,
                    'status' => 'Dilaporkan',
                    'isi' => 'Pembahasan integral lipat dua dan tiga beserta contoh soal dan penyelesaiannya.',
                    'alasan' => 'Terdapat bagian catatan yang dianggap kurang sesuai dengan materi.'
                ],
                [
                    'id' => 4,
                    'judul' => 'Ringkasan Jaringan Komputer — OSI Layer',
                    'penulis' => 'Dewi Anjani',
                    'jurusan' => 'Informatika',
                    'upvote' => 45,
                    'status' => 'Terbit',
                    'isi' => 'Ringkasan tujuh layer pada model OSI dan fungsi setiap layer.',
                    'alasan' => ''
                ]
            ];
        }

        if (!isset($_SESSION['pengguna_data'])) {
            $_SESSION['pengguna_data'] = [
                [
                    'id' => 1,
                    'nama' => 'Sarah Faradila',
                    'nim' => '2551506...003',
                    'jurusan' => 'Informatika',
                    'status' => 'Aktif'
                ],
                [
                    'id' => 2,
                    'nama' => 'Nazma Fairuz M.',
                    'nim' => '2551506...001',
                    'jurusan' => 'Sistem Informasi',
                    'status' => 'Aktif'
                ],
                [
                    'id' => 3,
                    'nama' => 'Bagas Wicaksono',
                    'nim' => '2551506...004',
                    'jurusan' => 'Teknik Informatika',
                    'status' => 'Aktif'
                ]
            ];
        }

        // AKSI PENGGUNA DAN CATATAN

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Aksi pengguna
            if (isset($_POST['user_action'])) {
                $id = (int) ($_POST['id'] ?? 0);
                $action = $_POST['user_action'];

                foreach ($_SESSION['pengguna_data'] as $key => $user) {
                    if ($user['id'] == $id) {

                        if ($action === 'block') {
                            $_SESSION['pengguna_data'][$key]['status'] = 'Diblokir';
                        }

                        if ($action === 'unblock') {
                            $_SESSION['pengguna_data'][$key]['status'] = 'Aktif';
                        }

                        if ($action === 'delete') {
                            unset($_SESSION['pengguna_data'][$key]);

                            $_SESSION['pengguna_data'] = array_values(
                                $_SESSION['pengguna_data']
                            );
                        }

                        break;
                    }
                }

                header('Location: ' . BASE_URL . '/?page=admin-dashboard');
                exit;
            }

            // Aksi catatan
            if (isset($_POST['note_action'])) {
                $id = (int) ($_POST['id'] ?? 0);
                $action = $_POST['note_action'];

                foreach ($_SESSION['catatan_data'] as $key => $note) {
                    if ($note['id'] == $id) {

                        if ($action === 'publish') {
                            $_SESSION['catatan_data'][$key]['status'] = 'Terbit';
                            $_SESSION['catatan_data'][$key]['alasan'] = '';
                        }

                        if ($action === 'delete') {
                            unset($_SESSION['catatan_data'][$key]);

                            $_SESSION['catatan_data'] = array_values(
                                $_SESSION['catatan_data']
                            );
                        }

                        break;
                    }
                }

                header('Location: ' . BASE_URL . '/?page=admin-dashboard');
                exit;
            }
        }

        // DATA DASHBOARD
        $catatan = $_SESSION['catatan_data'];
        $pengguna = $_SESSION['pengguna_data'];

        $totalCatatan = count($catatan);
        $totalPengguna = count($pengguna);

        $totalUpvote = 0;

        foreach ($catatan as $item) {
            $totalUpvote += (int) $item['upvote'];
        }

        $laporan = array_filter($catatan, function ($item) {
            return $item['status'] === 'Dilaporkan';
        });

        // DATA LAYOUT
        $page_title = 'Dashboard Admin';

        $user_name = $_SESSION['nama_lengkap'] ?? 'Administrator';
        $user_role = $_SESSION['role'] ?? 'admin';

        $active_menu = 'dashboard';

        $page_css = 'dashboard_admin.css';
        $page_js = 'dashboard_admin.js';

        // Menampung konten dashboard
        ob_start();

        require VIEW_PATH . '/admin/dashboard.php';

        $content = ob_get_clean();

        // Menampilkan layout utama
        require VIEW_PATH . '/layouts/main.php';

        
    }
}
