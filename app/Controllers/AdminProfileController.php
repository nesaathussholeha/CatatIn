
<?php

class AdminProfileController
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/?page=dashboard');
            exit;
        }

        $page_title = 'Profil Admin';
        $user_name = $_SESSION['nama_lengkap'] ?? 'Administrator';
        $user_role = $_SESSION['role'] ?? 'admin';
        $active_menu = 'profil-admin';

        $page_css = 'profil_admin.css';
        $page_js = 'profil_admin.js';

        ob_start();
        require VIEW_PATH . '/admin/profil.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}
