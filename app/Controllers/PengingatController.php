
<?php

require_once dirname(__DIR__) . '/Models/Pengingat.php';

class PengingatController
{
    private $model;

    public function __construct()
    {
        $this->model = new Pengingat();
    }

    // Halaman utama pengingat
    public function index()
    {
        // Memproses aksi dari form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->prosesAksi();
        }

        // Mengambil filter
        $filter = $_GET['status'] ?? 'semua';

        if (!in_array($filter, ['semua', 'belum', 'selesai'], true)) {
            $filter = 'semua';
        }

        // Mengambil urutan
        $urut = $_GET['urut'] ?? 'terdekat';

        if (!in_array($urut, ['terdekat', 'terjauh'], true)) {
            $urut = 'terdekat';
        }

        // Mengambil data dari Model
        $daftar = $this->model->getFiltered($filter, $urut);

        // Pengaturan layout
        $page_title = 'Tugas Belajar';
        $active_menu = 'tugas';

        $user_name = 'Username';
        $user_role = 'Role';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        // Memanggil View
        require dirname(__DIR__) . '/Views/pengingat/index.php';
    }

    // Memproses tambah, edit, hapus, dan toggle
    private function prosesAksi()
    {
        $aksi = $_POST['aksi'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);

        $judul = trim($_POST['judul'] ?? '');
        $deadline = $_POST['deadline'] ?? '';

        switch ($aksi) {
            case 'tambah':
                $this->model->tambah($judul, $deadline);
                break;

            case 'edit':
                $this->model->edit($id, $judul, $deadline);
                break;

            case 'hapus':
                $this->model->hapus($id);
                break;

            case 'toggle':
                $this->model->toggle($id);
                break;
        }

        // Kembali ke halaman sebelumnya
        $qs = $_SERVER['QUERY_STRING'] ?? '';

        header(
            'Location: tugas.php' .
            ($qs !== '' ? '?' . $qs : '')
        );

        exit;
    }
}
