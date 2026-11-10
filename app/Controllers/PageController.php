<?php

class PageController
{
    public function index()
    {
        // Periksa apakah pengguna sudah login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        // Periksa role pengguna
        if (strtolower($_SESSION['role'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . '/?page=admin-dashboard');
            exit;
        }

        // Tampilkan dashboard user
        $page_title = 'Dashboard User';
        $user_name = $_SESSION['nama_lengkap'] ?? 'User';
        $user_role = $_SESSION['role'] ?? 'user';
        $active_menu = 'dashboard';

        require VIEW_PATH . '/user/home.php';
    }
}