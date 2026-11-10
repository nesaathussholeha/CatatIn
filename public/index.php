<?php

session_start();

// Memanggil konfigurasi utama
require_once __DIR__ . '/../config/app.php';

// Memanggil koneksi database
require_once ROOT_PATH . '/config/koneksi.php';

// Memanggil Model
require_once ROOT_PATH . '/app/Models/User.php';

// Memanggil Controller
require_once ROOT_PATH . '/app/Controllers/AuthController.php';
require_once ROOT_PATH . '/app/Controllers/PageController.php';
require_once ROOT_PATH . '/app/Controllers/MasterDataController.php';
require_once ROOT_PATH . '/app/Controllers/AdminDashboardController.php';
require_once ROOT_PATH . '/app/Controllers/UserDashboardController.php';


// Halaman default adalah login
$page = $_GET['page'] ?? 'login';

switch ($page) {

    // LOGIN DAN REGISTER
    case 'login':

        $user = new User($pdo);
        $controller = new AuthController($user);
        $controller->handle();

        break;


    case 'admin-dashboard':

        if (
            !isset($_SESSION['role']) ||
            strtolower($_SESSION['role']) !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new AdminDashboardController();
        $controller->index();
        break;


    case 'dashboard':

        $controller = new UserDashboardController();
        $controller->index();

        break;


    // MASTER DATA (KHUSUS ADMIN)
    case 'master-data':

        if (
            !isset($_SESSION['role']) ||
            strtolower($_SESSION['role']) !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new MasterDataController();
        $controller->index();

        break;

    case 'logout':
        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . '/?page=login');
        exit;

        // HALAMAN TIDAK DIKENAL
    default:

        header('Location: ?page=login');
        exit();
}
