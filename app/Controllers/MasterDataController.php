
<?php

require_once ROOT_PATH . '/app/Models/MasterData.php';

class MasterDataController
{
    private $model;

    public function __construct()
    {
        $this->model = new MasterData();
    }

    public function index()
    {
        // Cek login
        if (!isset($_SESSION['role'])) {
            header('Location: ' . BASE_URL . '/../auth/login.php');
            exit;
        }

        // Hanya Admin yang boleh mengakses
        if (strtolower($_SESSION['role']) !== 'admin') {
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }

        // Proses form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $tab = $_POST['tab'] ?? 'jurusan';
            $id = $_POST['id'] ?? '';

            if ($action === 'save') {
                $kode = trim($_POST['kode'] ?? '');
                $nama = trim($_POST['nama'] ?? '');
                $extra = trim($_POST['extra'] ?? '');

                $this->model->save(
                    $tab,
                    $id,
                    $kode,
                    $nama,
                    $extra
                );

            } elseif ($action === 'toggle_status') {

                $this->model->toggleStatus($tab, $id);

            } elseif ($action === 'delete') {

                $this->model->delete($tab, $id);
            }

            header(
                'Location: ' . BASE_URL .
                '/index.php?page=master-data&tab=' .
                urlencode($tab)
            );

            exit;
        }

        // Ambil tab aktif
        $allowedTabs = [
            'jurusan',
            'matakuliah',
            'kategori'
        ];

        $active_tab = $_GET['tab'] ?? 'jurusan';

        if (!in_array($active_tab, $allowedTabs, true)) {
            $active_tab = 'jurusan';
        }

        // Ambil seluruh data
        $master_data = $this->model->getAll();

        // Data layout
        $page_title = 'Master Data';
        $active_menu = 'master_data';

        $user_name = $_SESSION['nama'] ?? 'Administrator 1';
        $user_role = 'Admin';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        // CSS dan JS halaman
        $page_css = 'master_data.css';
        $page_js = 'master_data.js';

        // Render halaman
        ob_start();

        require VIEW_PATH . '/admin/master_data.php';

        $content = ob_get_clean();

        // Gunakan layout bersama
        require VIEW_PATH . '/layouts/main.php';
    }
}
