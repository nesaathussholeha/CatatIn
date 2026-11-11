<?php

require_once ROOT_PATH . '/app/Models/ReportAdmin.php';

class ReportAdminController
{
    public function index()
    {
        // Wajib login & admin
        if (
            empty($_SESSION['user_id']) ||
            strtolower($_SESSION['role'] ?? '') !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // Data
        $model   = new ReportAdmin();
        $laporan = $model->getAll();

        // Variabel untuk layout
        $page_title  = 'Laporan Masuk';
        $user_name   = $_SESSION['nama_lengkap'] ?? 'Administrator';
        $user_role   = $_SESSION['role'] ?? 'admin';
        $active_menu = 'laporan';          // sama dengan yang dicek di sidebar.php
        $page_css    = 'report_admin.css';
        $page_js     = 'report_admin.js';

        // Render view ke dalam $content, lalu bungkus dengan layout
        ob_start();
        require VIEW_PATH . '/admin/report_admin.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}