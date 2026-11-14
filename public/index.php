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
require_once ROOT_PATH . '/app/Controllers/ReportUserController.php';
require_once ROOT_PATH . '/app/Controllers/ReportAdminController.php';
require_once ROOT_PATH . '/app/Controllers/AdminUserController.php';
require_once ROOT_PATH . '/app/Controllers/PengingatController.php';

// Controller Kelola Catatan
require_once ROOT_PATH . '/app/Controllers/PengelolaCatatanController.php';
require_once ROOT_PATH . '/app/Controllers/AdminProfileController.php';
require_once ROOT_PATH . '/app/Models/ProfileUser.php';
require_once ROOT_PATH . '/app/Controllers/UserProfileController.php';
require_once ROOT_PATH . '/app/Controllers/ProfilePenulisController.php';

// Halaman default adalah login
$page = $_GET['page'] ?? 'login';

switch ($page) {

    // LOGIN DAN REGISTER
    case 'login':

        $user = new User($pdo);
        $controller = new AuthController($user);
        $controller->handle();

        break;


    // ADMIN DASHBOARD
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


    // USER DASHBOARD
    case 'dashboard':
        require_once ROOT_PATH . '/app/Controllers/UserDashboardController.php';
        (new UserDashboardController())->index();
        break;

    case 'koleksi':
        require_once ROOT_PATH . '/app/Controllers/PlaylistController.php';
        (new PlaylistController())->index();
        break;

    // PENGINGAT
    case 'pengingat':

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new PengingatController();
        $controller->index();

        break;

    case 'profil-user':

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new UserProfileController();
        $controller->index();

        break;

    case 'profile-penulis':
        $controller = new ProfilePenulisController();
        $controller->index();
        break;
    // KELOLA CATATAN (KHUSUS ADMIN)
    case 'kelola_catatan':

        if (
            !isset($_SESSION['role']) ||
            strtolower($_SESSION['role']) !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new PengelolaCatatanController();
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


    // FITUR REPORT USER
    case 'report-user':
    case 'detail-catatan':

        $controller = new ReportUserController();
        $controller->index();

        break;


    // FITUR REPORT ADMIN
    case 'laporan-admin':

        if (
            !isset($_SESSION['role']) ||
            strtolower($_SESSION['role']) !== 'admin'
        ) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        $controller = new ReportAdminController();
        $controller->index();

        break;


    // KELOLA PENGGUNA
    case 'kelola-pengguna':

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/?page=dashboard');
            exit;
        }

        $controller = new AdminUserController();
        $controller->index();

        break;


    // PROFIL ADMIN
    case 'profil-admin':

        $controller = new AdminProfileController();
        $controller->index();

        break;


    // LOGOUT
    case 'logout':

        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . '/?page=login');
        exit;


        // HALAMAN TIDAK DIKENAL
    default:

        header('Location: ' . BASE_URL . '/?page=login');
        exit;
}
