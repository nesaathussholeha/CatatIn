<?php

require_once __DIR__ . '/../Models/ReportAdminModel.php';

class ReportAdminController {
    private $reportAdminModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->reportAdminModel = new ReportAdminModel();
    }

    public function index() {
        // Handling API Endpoint/AJAX request
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

        // Variable yang dikirim ke View
        $pageTitle  = "Kelola Laporan";
        $activeMenu = "laporan";

        $userName = $_SESSION['user_name'] ?? 'Administrator 1';
        $userRole = ucfirst($_SESSION['role'] ?? 'Admin');
        $userInit = strtoupper(substr($userName, 0, 1));

        // Mengambil data dari Model
        $laporan = $this->reportAdminModel->getAllReports();

        // Panggil View Admin
        require_once __DIR__ . '/../Views/admin/report_admin.php';
    }
}