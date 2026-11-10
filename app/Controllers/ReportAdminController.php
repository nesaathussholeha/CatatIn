<?php

require_once ROOT_PATH . '/app/Models/ReportAdminModel.php';

class ReportAdminController {
    private $reportAdminModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->reportAdminModel = new ReportAdminModel();
    }

    public function index() {
        // Handling AJAX Actions (Status / Hapus)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['aksi'])) {
            header('Content-Type: application/json; charset=utf-8');
            $aksi = $_GET['aksi'];

            if ($aksi === 'update-status') {
                $id = (int)($_POST['id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $success = $this->reportAdminModel->updateStatus($id, $status);
                echo json_encode(['success' => $success]);
                exit;
            }

            if ($aksi === 'delete-laporan') {
                $id = (int)($_POST['id'] ?? 0);
                $success = $this->reportAdminModel->deleteReport($id);
                echo json_encode(['success' => $success]);
                exit;
            }
        }

        // Variable untuk Layout main.php (Sistem otomatis mengenali role Admin)
        $page_title  = "Kelola Laporan";
        $active_menu = "laporan";
        $user_name   = $_SESSION['user_name'] ?? 'Administrator 1';
        $user_role   = $_SESSION['role'] ?? 'Admin'; // Role Admin
        $user_initial = strtoupper(substr($user_name, 0, 1));

        $page_css = "report_admin.css";
        $page_js  = "report_admin.js";

        // Mengambil data laporan
        $laporan = $this->reportAdminModel->getAllReports();

        // Tangkap output dari app/Views/admin/report_admin.php ke dalam variabel $content
        ob_start();
        require VIEW_PATH . '/admin/report_admin.php';
        $content = ob_get_clean();

        // Render Layout Utama
        require VIEW_PATH . '/layouts/main.php';
    }
}