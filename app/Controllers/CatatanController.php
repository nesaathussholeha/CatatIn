<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/Catatan.php';

class CatatanController
{
    private Catatan $model;

    private array $daftarJurusan = [
        'Informatika',
        'Sistem Informasi',
        'Teknik Komputer',
        'Elektro'
    ];

    private array $daftarKategori = [
        'Rangkuman',
        'Soal Ujian',
        'Modul'
    ];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->model = new Catatan();

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
    }

    private function validasiCsrf(): bool
    {
        $token = $_POST['csrf'] ?? '';
        $csrfSession = $_SESSION['csrf'] ?? '';

        return is_string($token)
            && is_string($csrfSession)
            && $csrfSession !== ''
            && hash_equals($csrfSession, $token);
    }

    // Menampilkan Catatan Saya
    public function index(): void
    {
        $catatanSaya = $this->model->ambilSemua();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $page_title = 'Catatan Saya';
        $active_menu = 'catatan_saya';

        $user_name = $_SESSION['nama'] ?? 'Username';
        $user_role = $_SESSION['role'] ?? 'User';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        $csrf = $_SESSION['csrf'];

        require ROOT_PATH . '/app/Views/user/catatan_saya.php';
    }

    // Form unggah dan edit catatan
    public function form(): void
    {
        $galat = [];
        $idForm = 0;

        $input = [
            'judul' => '',
            'matkul' => '',
            'jurusan' => 'Informatika',
            'kategori' => 'Rangkuman',
            'deskripsi' => '',
            'tautan' => '',
            'is_anonim' => false
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validasiCsrf()) {
                $galat[] = 'Sesi formulir tidak valid.';
            }

            $idForm = (int) ($_POST['id'] ?? 0);

            foreach ([
                'judul',
                'matkul',
                'jurusan',
                'kategori',
                'deskripsi',
                'tautan'
            ] as $field) {
                $value = $_POST[$field] ?? '';
                $input[$field] = is_string($value)
                    ? trim($value)
                    : '';
            }

            $input['is_anonim'] =
                ($_POST['is_anonim'] ?? '') === '1';

            if (
                $input['judul'] === '' ||
                mb_strlen($input['judul']) > 150
            ) {
                $galat[] = 'Judul wajib diisi maksimal 150 karakter.';
            }

            if (
                $input['matkul'] === '' ||
                mb_strlen($input['matkul']) > 100
            ) {
                $galat[] = 'Mata kuliah wajib diisi maksimal 100 karakter.';
            }

            if (!in_array($input['jurusan'], $this->daftarJurusan, true)) {
                $galat[] = 'Jurusan tidak valid.';
            }

            if (!in_array($input['kategori'], $this->daftarKategori, true)) {
                $galat[] = 'Tipe berkas tidak valid.';
            }

            if (
                $input['deskripsi'] === '' ||
                mb_strlen($input['deskripsi']) > 255
            ) {
                $galat[] = 'Deskripsi wajib diisi maksimal 255 karakter.';
            }

            $tautan = $input['tautan'];

            if ($tautan !== '' && !preg_match('~^https?://~i', $tautan)) {
                $tautan = 'https://' . $tautan;
            }

            $host = parse_url($tautan, PHP_URL_HOST);

            if (
                $tautan === '' ||
                mb_strlen($tautan) > 500 ||
                filter_var($tautan, FILTER_VALIDATE_URL) === false ||
                !is_string($host) ||
                strpos($host, '.') === false
            ) {
                $galat[] = 'Tautan berkas tidak valid.';
            } else {
                $input['tautan'] = $tautan;
            }

            if ($galat === []) {
                $data = [
                    'judul' => $input['judul'],
                    'matkul' => $input['matkul'],
                    'jurusan' => $input['jurusan'],
                    'tipe' => $input['kategori'],
                    'deskripsi' => $input['deskripsi'],
                    'tautan' => $input['tautan'],
                    'anonim' => $input['is_anonim']
                ];

                if ($idForm > 0) {
                    $berhasil = $this->model->update($idForm, $data);

                    $_SESSION['flash'] = [
                        'pesan' => $berhasil
                            ? 'Catatan berhasil diperbarui.'
                            : 'Catatan tidak ditemukan.',
                        'galat' => !$berhasil
                    ];
                } else {
                    $this->model->tambah($data);

                    $_SESSION['flash'] = [
                        'pesan' => 'Catatan berhasil disimpan.',
                        'galat' => false
                    ];
                }

                header('Location: ' . BASE_URL . '/?page=catatan-saya');
                exit;
            }
        } elseif (isset($_GET['edit'])) {

            $idForm = (int) $_GET['edit'];
            $catatan = $this->model->ambilById($idForm);

            if ($catatan !== null) {
                $input = [
                    'judul' => $catatan['judul'],
                    'matkul' => $catatan['matkul'],
                    'jurusan' => $catatan['jurusan'],
                    'kategori' => $catatan['tipe'],
                    'deskripsi' => $catatan['deskripsi'],
                    'tautan' => $catatan['tautan'],
                    'is_anonim' => $catatan['anonim']
                ];
            } else {
                $idForm = 0;
                $galat[] = 'Catatan yang ingin diedit tidak ditemukan.';
            }
        }

        $modeEdit = $idForm > 0;

        $page_title = $modeEdit ? 'Edit Catatan' : 'Unggah Catatan';
        $active_menu = 'unggah';

        $user_name = $_SESSION['nama'] ?? 'Username';
        $user_role = $_SESSION['role'] ?? 'User';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        $daftarJurusan = $this->daftarJurusan;
        $daftarKategori = $this->daftarKategori;

        $csrf = $_SESSION['csrf'];

        require ROOT_PATH . '/app/Views/user/unggah_catatan.php';
    }

    // Menghapus catatan
    public function hapus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?page=catatan-saya');
            exit;
        }

        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$this->validasiCsrf()) {
            $_SESSION['flash'] = [
                'pesan' => 'Sesi tidak valid.',
                'galat' => true
            ];
        } elseif ($id === false || $id === null || $id <= 0) {
            $_SESSION['flash'] = [
                'pesan' => 'Permintaan tidak valid.',
                'galat' => true
            ];
        } else {
            $berhasil = $this->model->hapus($id);

            $_SESSION['flash'] = [
                'pesan' => $berhasil
                    ? 'Catatan berhasil dihapus.'
                    : 'Catatan tidak ditemukan.',
                'galat' => !$berhasil
            ];
        }

        header('Location: ' . BASE_URL . '/?page=catatan-saya');
        exit;
    }
}