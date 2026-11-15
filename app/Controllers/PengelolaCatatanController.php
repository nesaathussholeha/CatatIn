
<?php

require_once ROOT_PATH . '/app/Models/Catatan.php';

class PengelolaCatatanController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // PROSES FORM
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            // TAMBAH CATATAN
            if ($action === 'add') {

                $data = [
                    'judul' => $_POST['judul'] ?? '',
                    'penulis' => $_POST['penulis'] ?? '',
                    'jurusan' => $_POST['jurusan'] ?? '',
                    'isi' => $_POST['isi'] ?? ''
                ];

                if (
                    trim($data['judul']) !== '' &&
                    trim($data['penulis']) !== '' &&
                    trim($data['jurusan']) !== '' &&
                    trim($data['isi']) !== ''
                ) {
                    Catatan::tambah($data);
                }
            }

            // EDIT CATATAN
            elseif ($action === 'edit') {

                $id = (int) ($_POST['id'] ?? 0);

                $data = [
                    'judul' => $_POST['judul'] ?? '',
                    'penulis' => $_POST['penulis'] ?? '',
                    'jurusan' => $_POST['jurusan'] ?? '',
                    'isi' => $_POST['isi'] ?? ''
                ];

                if (
                    $id > 0 &&
                    trim($data['judul']) !== '' &&
                    trim($data['penulis']) !== '' &&
                    trim($data['jurusan']) !== '' &&
                    trim($data['isi']) !== ''
                ) {
                    Catatan::edit($id, $data);
                }
            }

            // UBAH STATUS
            elseif ($action === 'status') {

                $id = (int) ($_POST['id'] ?? 0);
                $status = $_POST['status'] ?? '';

                if ($id > 0) {
                    Catatan::ubahStatus($id, $status);
                }
            }

            // HAPUS CATATAN
            elseif ($action === 'delete') {

                $id = (int) ($_POST['id'] ?? 0);

                if ($id > 0) {
                    Catatan::hapus($id);
                }
            }

            // KEMBALI KE HALAMAN KELOLA CATATAN
            header('Location: ' . BASE_URL . '/?page=kelola_catatan');
            exit;
        }

        // AMBIL DATA CATATAN
        $catatan = Catatan::semua();

        // DATA LAYOUT ADMIN
        $page_title = 'Kelola Catatan';
        $user_name = $_SESSION['user_name'] ?? 'Administrator';
        $user_role = 'Admin';
        $active_menu = 'kelola_catatan';

        $page_css = 'kelola_catatan.css';
        $page_js = 'kelola_catatan.js';

        // SIAPKAN VIEW
        ob_start();

        require VIEW_PATH . '/admin/kelola_catatan.php';

        $content = ob_get_clean();

        // TAMPILKAN LAYOUT UTAMA
        require VIEW_PATH . '/layouts/main.php';
    }
}
