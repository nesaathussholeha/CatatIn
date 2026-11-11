<?php

require_once ROOT_PATH . '/app/Models/Playlist.php';

class PlaylistController
{
    private $model;

    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=login');
            exit;
        }

        if (strtolower($_SESSION['role'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . '/?page=admin-dashboard');
            exit;
        }

        $this->model = new Playlist();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $this->model->create(
                    trim($_POST['nama'] ?? ''),
                    trim($_POST['deskripsi'] ?? '')
                );
            } elseif ($action === 'delete') {
                $this->model->delete($_POST['id'] ?? 0);
            }

            header('Location: ' . BASE_URL . '/index.php?page=koleksi');
            exit;
        }

        $playlists = $this->model->getAll();

        $page_title  = 'Playlist Saya';
        $active_menu = 'koleksi';
        $user_name   = $_SESSION['nama_lengkap'] ?? 'User';
        $user_role   = $_SESSION['role'] ?? 'user';

        $page_css = 'playlist.css';
        $page_js  = 'playlist.js';

        ob_start();

        require VIEW_PATH . '/user/playlist.php';

        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}