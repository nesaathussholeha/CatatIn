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

    // Helper untuk keamanan output
    private function e(?string $teks): string
    {
        return htmlspecialchars(
            (string) $teks,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    // Menampilkan halaman Catatan Saya
    public function index(): void
    {
        $catatanSaya = $this->model->ambilSemua();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $page_title = 'Catatan Saya';
        $active_menu = 'catatan_saya';

        $user_name = 'Username';
        $user_role = 'Role';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        $csrf = $_SESSION['csrf'];

        require __DIR__ . '/../Views/catatan/index.php';
    }

    // Menampilkan formulir unggah atau edit
    public function form(): void
    {
        $galat = [];
        $idForm = 0;

        // Nilai awal formulir
        $input = [
            'judul' => '',
            'matkul' => '',
            'jurusan' => 'Informatika',
            'kategori' => 'Rangkuman',
            'deskripsi' => '',
            'tautan' => '',
            'is_anonim' => false
        ];

        // Jika formulir dikirim
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validasi CSRF
            $token = $_POST['csrf'] ?? '';

            if (
                !is_string($token) ||
                !hash_equals($_SESSION['csrf'], $token)
            ) {
                $galat[] = 'Sesi formulir tidak valid.';
            }

            $idForm = (int) ($_POST['id'] ?? 0);

            // Mengambil input formulir
            foreach (
                [
                    'judul',
                    'matkul',
                    'jurusan',
                    'kategori',
                    'deskripsi',
                    'tautan'
                ] as $field
            ) {
                $value = $_POST[$field] ?? '';

                $input[$field] = is_string($value)
                    ? trim($value)
                    : '';
            }

            $input['is_anonim'] =
                ($_POST['is_anonim'] ?? '') === '1';

            // Validasi judul
            if (
                $input['judul'] === '' ||
                mb_strlen($input['judul']) > 150
            ) {
                $galat[] =
                    'Judul wajib diisi maksimal 150 karakter.';
            }

            // Validasi mata kuliah
            if (
                $input['matkul'] === '' ||
                mb_strlen($input['matkul']) > 100
            ) {
                $galat[] =
                    'Mata kuliah wajib diisi maksimal 100 karakter.';
            }

            // Validasi jurusan
            if (
                !in_array(
                    $input['jurusan'],
                    $this->daftarJurusan,
                    true
                )
            ) {
                $galat[] = 'Jurusan tidak valid.';
            }

            // Validasi kategori
            if (
                !in_array(
                    $input['kategori'],
                    $this->daftarKategori,
                    true
                )
            ) {
                $galat[] = 'Tipe berkas tidak valid.';
            }

            // Validasi deskripsi
            if (
                $input['deskripsi'] === '' ||
                mb_strlen($input['deskripsi']) > 255
            ) {
                $galat[] =
                    'Deskripsi wajib diisi maksimal 255 karakter.';
            }

            // Validasi tautan
            $tautan = $input['tautan'];

            if (
                $tautan !== '' &&
                !preg_match('~^https?://~i', $tautan)
            ) {
                $tautan = 'https://' . $tautan;
            }

            $host = parse_url($tautan, PHP_URL_HOST);

            if (
                $tautan === '' ||
                mb_strlen($tautan) > 500 ||
                filter_var(
                    $tautan,
                    FILTER_VALIDATE_URL
                ) === false ||
                !is_string($host) ||
                strpos($host, '.') === false
            ) {
                $galat[] = 'Tautan berkas tidak valid.';
            } else {
                $input['tautan'] = $tautan;
            }

            // Jika tidak ada kesalahan, simpan data
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

                // Memperbarui catatan
                if ($idForm > 0) {

                    $berhasil = $this->model->update(
                        $idForm,
                        $data
                    );

                    $_SESSION['flash'] = $berhasil
                        ? [
                            'pesan' => 'Catatan berhasil diperbarui.',
                            'galat' => false
                        ]
                        : [
                            'pesan' => 'Catatan tidak ditemukan.',
                            'galat' => true
                        ];
                } else {

                    // Menambahkan catatan baru
                    $this->model->tambah($data);

                    $_SESSION['flash'] = [
                        'pesan' => 'Catatan berhasil disimpan.',
                        'galat' => false
                    ];
                }

                header(
                    'Location: ../catatan_saya/catatan_saya.php'
                );
                exit;
            }
        } elseif (isset($_GET['edit'])) {

            // Mengambil data untuk diedit
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

                $galat[] =
                    'Catatan yang ingin diedit tidak ditemukan.';
            }
        }

        // Menentukan mode formulir
        $modeEdit = $idForm > 0;

        $page_title = $modeEdit ? 'Edit catatan' : 'Unggah catatan';
        $active_menu = 'unggah';

        $user_name = 'Username';
        $user_role = 'Role';
        $user_initial = strtoupper(substr($user_name, 0, 1));

        $daftarJurusan = $this->daftarJurusan;
        $daftarKategori = $this->daftarKategori;

        $csrf = $_SESSION['csrf'];

        // Memanggil View
        require __DIR__ . '/../Views/catatan/form.php';
    }

    // Menghapus catatan
    public function hapus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            !is_string($token) ||
            !hash_equals($_SESSION['csrf'], $token)
        ) {
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

        header('Location: index.php');
        exit;
    }
}
