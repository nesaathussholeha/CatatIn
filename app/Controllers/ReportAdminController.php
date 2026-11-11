<?php

require_once ROOT_PATH . '/app/Models/ReportAdmin.php';

class ReportAdminController
{
    public function index()
    {
        // Hanya admin yang boleh membuka halaman ini
        if (
            empty($_SESSION['user_id']) ||
            strtolower($_SESSION['role'] ?? '') !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // Ambil data dari Model
        $model   = new ReportAdmin($GLOBALS['pdo'] ?? null);
        $laporan = $model->getAll();

        // Variabel untuk layout main.php
        $page_title  = 'Laporan Masuk';
        $active_menu = 'laporan';
        $user_name   = $_SESSION['user_name'] ?? 'Administrator';
        $user_role   = $_SESSION['role'] ?? 'Admin';
        $page_css    = 'report_admin.css';
        $page_js     = 'report_admin.js';

        // Render isi halaman ke dalam $content
        ob_start();
        require VIEW_PATH . '/admin/report_admin.php';
        $content = ob_get_clean();

        // Tampilkan lewat layout utama
        require VIEW_PATH . '/layouts/main.php';
    }
}