<?php

require_once __DIR__ . '/../Models/ReportUser.php';

class ReportUserController
{
    private ReportUser $model;

    public function __construct()
    {
        $this->model = new ReportUser();
    }

    /** Halaman detail catatan + modal laporan  (GET ?page=report-user&id=1) */
    public function index(): void
    {
        $id      = (int) ($_GET['id'] ?? 1);
        $catatan = $this->model->findCatatan($id);

        if (!$catatan) {
            http_response_code(404);
            echo 'Catatan tidak ditemukan.';
            return;
        }

        $topik        = $this->model->getTopik($id);
        $halaman_lain = $this->model->getHalamanLain($id);

        $this->render('user/report_user', [
            'page_title'   => 'Detail Catatan',
            'active_menu'  => 'dashboard',
            'user_name'    => $_SESSION['nama_lengkap'] ?? 'User',
            'user_role'    => $_SESSION['role'] ?? 'user',
            'page_css'     => 'report_user.css',
            'page_js'      => 'report_user.js',
            'catatan'      => $catatan,
            'topik'        => $topik,
            'halaman_lain' => $halaman_lain,
        ]);
    }

    /** Kirim laporan via fetch  (POST ?page=report-user-kirim) */
    public function kirim(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
            return;
        }

        $file   = $_FILES['file'] ?? null;
        $errors = $this->model->validasi($_POST, $file);

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        // Simpan file (kalau ada)
        $namaFile = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $folder = dirname(__DIR__, 2) . '/public/uploads/laporan/';
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            $namaFile = uniqid('laporan_', true) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $folder . $namaFile);
        }

        $this->model->simpan([
            'catatan_id' => (int) ($_POST['catatan_id'] ?? 0),
            'judul'      => trim($_POST['judul']),
            'jenis'      => trim($_POST['jenis']),
            'deskripsi'  => trim($_POST['deskripsi'] ?? ''),
            'file'       => $namaFile,
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Laporan berhasil dikirim. Terima kasih atas masukan Anda!',
        ]);
    }

    /** Render view ke dalam layouts/main.php */
    private function render(string $view, array $data = []): void
    {
        extract($data);

        ob_start();
        require VIEW_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}