<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/Catatan.php';

// Helper tampilan (dipakai View), digabung di sini agar tidak perlu file tambahan
defined('APP_NAME') || define('APP_NAME', 'CatatIn');

if (!function_exists('e')) {
    function e(?string $teks): string
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('kelasTipe')) {
    function kelasTipe(string $tipe): string
    {
        return match ($tipe) {
            'Soal Ujian' => 'badge-soal',
            'Modul'      => 'badge-modul',
            default      => 'badge-rangkuman',
        };
    }
}

class CatatanController
{
    private Catatan $model;

    private array $daftarJurusan   = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Elektro'];
    private array $daftarKategori  = ['Rangkuman', 'Soal Ujian', 'Modul'];
    private array $daftarVisibilitas = ['pribadi' => 'Pribadi', 'publik' => 'Publik'];

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

    /* ---------- Helper internal ---------- */

    private function validasiCsrf(): bool
    {
        $token = $_POST['csrf'] ?? '';
        $csrfSession = $_SESSION['csrf'] ?? '';

        return is_string($token) && is_string($csrfSession)
            && $csrfSession !== '' && hash_equals($csrfSession, $token);
    }

    private function namaUser(): string
    {
        return (string) ($_SESSION['nama'] ?? 'Username');
    }

    private function milikSaya(?array $catatan): bool
    {
        return $catatan !== null && $catatan['penulis'] === $this->namaUser();
    }

    private function flash(string $pesan, bool $galat): void
    {
        $_SESSION['flash'] = ['pesan' => $pesan, 'galat' => $galat];
    }

    private function redirectKeCatatanSaya(): never
    {
        header('Location: ' . BASE_URL . '/?page=catatan-saya');
        exit;
    }

    /** Sidebar dirender di sini, satu sumber (layouts/sidebar.php) untuk semua halaman. */
    private function renderSidebar(string $activeMenu): string
    {
        $is_admin    = false;
        $active_menu = $activeMenu;

        ob_start();
        require ROOT_PATH . '/app/Views/partials/sidebar.php';
        return (string) ob_get_clean();
    }

    /** Variabel layout yang dibutuhkan semua halaman user. */
    private function dataLayout(string $judul, string $menu): array
    {
        $nama = $this->namaUser();

        return [
            'page_title'    => $judul,
            'active_menu'   => $menu,
            'sidebar'       => $this->renderSidebar($menu),
            'user_name'     => $nama,
            'user_role'     => (string) ($_SESSION['role'] ?? 'User'),
            'user_initial'  => strtoupper(substr($nama, 0, 1)),
            'csrf'          => $_SESSION['csrf'],
        ];
    }

    private function render(string $view, array $data): void
    {
        extract($data, EXTR_SKIP);
        require ROOT_PATH . '/app/Views/user/' . $view . '.php';
    }

    /* ---------- Aksi ---------- */

    // Halaman Catatan Saya
    public function index(): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->render('catatan_saya', $this->dataLayout('Catatan Saya', 'catatan_saya') + [
            'catatanSaya' => $this->model->ambilMilik($this->namaUser()),
            'flash'       => $flash,
        ]);
    }

    // Form unggah & edit
    public function form(): void
    {
        $galat  = [];
        $idForm = 0;
        $input  = [
            'judul' => '', 'matkul' => '', 'jurusan' => 'Informatika',
            'kategori' => 'Rangkuman', 'deskripsi' => '', 'tautan' => '',
            'visibilitas' => 'pribadi', 'is_anonim' => false,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validasiCsrf()) {
                $galat[] = 'Sesi formulir tidak valid.';
            }

            $idForm = (int) ($_POST['id'] ?? 0);

            foreach (['judul', 'matkul', 'jurusan', 'kategori', 'deskripsi', 'tautan', 'visibilitas'] as $field) {
                $value = $_POST[$field] ?? '';
                $input[$field] = is_string($value) ? trim($value) : '';
            }
            $input['is_anonim'] = ($_POST['is_anonim'] ?? '') === '1';

            // Edit hanya boleh untuk pemilik catatan
            if ($idForm > 0 && !$this->milikSaya($this->model->ambilById($idForm))) {
                $galat[] = 'Catatan tidak ditemukan.';
            }

            if ($input['judul'] === '' || mb_strlen($input['judul']) > 150) {
                $galat[] = 'Judul wajib diisi maksimal 150 karakter.';
            }
            if ($input['matkul'] === '' || mb_strlen($input['matkul']) > 100) {
                $galat[] = 'Mata kuliah wajib diisi maksimal 100 karakter.';
            }
            if (!in_array($input['jurusan'], $this->daftarJurusan, true)) {
                $galat[] = 'Jurusan tidak valid.';
            }
            if (!in_array($input['kategori'], $this->daftarKategori, true)) {
                $galat[] = 'Tipe berkas tidak valid.';
            }
            if (!array_key_exists($input['visibilitas'], $this->daftarVisibilitas)) {
                $galat[] = 'Visibilitas tidak valid.';
            }
            if ($input['deskripsi'] === '' || mb_strlen($input['deskripsi']) > 255) {
                $galat[] = 'Deskripsi wajib diisi maksimal 255 karakter.';
            }

            $tautan = $input['tautan'];
            if ($tautan !== '' && !preg_match('~^https?://~i', $tautan)) {
                $tautan = 'https://' . $tautan;
            }
            $host = $tautan !== '' ? parse_url($tautan, PHP_URL_HOST) : null;

            if (
                $tautan === '' || mb_strlen($tautan) > 500
                || filter_var($tautan, FILTER_VALIDATE_URL) === false
                || !is_string($host) || strpos($host, '.') === false
            ) {
                $galat[] = 'Tautan berkas tidak valid.';
            } else {
                $input['tautan'] = $tautan;
            }

            if ($galat === []) {
                $data = [
                    'judul'       => $input['judul'],
                    'matkul'      => $input['matkul'],
                    'jurusan'     => $input['jurusan'],
                    'tipe'        => $input['kategori'],
                    'deskripsi'   => $input['deskripsi'],
                    'tautan'      => $input['tautan'],
                    'visibilitas' => $input['visibilitas'],
                    'anonim'      => $input['is_anonim'],
                    'penulis'     => $this->namaUser(),
                ];

                if ($idForm > 0) {
                    $this->model->update($idForm, $data);
                    $this->flash('Catatan berhasil diperbarui.', false);
                } else {
                    $this->model->tambah($data);
                    $this->flash('Catatan berhasil disimpan.', false);
                }

                $this->redirectKeCatatanSaya();
            }
        } elseif (isset($_GET['edit'])) {
            $idForm  = (int) $_GET['edit'];
            $catatan = $this->model->ambilById($idForm);

            if ($this->milikSaya($catatan)) {
                $input = [
                    'judul'       => $catatan['judul'],
                    'matkul'      => $catatan['matkul'],
                    'jurusan'     => $catatan['jurusan'],
                    'kategori'    => $catatan['tipe'],
                    'deskripsi'   => $catatan['deskripsi'],
                    'tautan'      => $catatan['tautan'],
                    'visibilitas' => $catatan['visibilitas'],
                    'is_anonim'   => (bool) $catatan['anonim'],
                ];
            } else {
                $idForm  = 0;
                $galat[] = 'Catatan yang ingin diedit tidak ditemukan.';
            }
        }

        $modeEdit = $idForm > 0;

        $this->render('unggah_catatan', $this->dataLayout($modeEdit ? 'Edit Catatan' : 'Unggah Catatan', 'unggah') + [
            'galat'            => $galat,
            'idForm'           => $idForm,
            'input'            => $input,
            'modeEdit'         => $modeEdit,
            'daftarJurusan'    => $this->daftarJurusan,
            'daftarKategori'   => $this->daftarKategori,
            'daftarVisibilitas' => $this->daftarVisibilitas,
        ]);
    }

    // Hapus catatan
    public function hapus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectKeCatatanSaya();
        }

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$this->validasiCsrf()) {
            $this->flash('Sesi tidak valid.', true);
        } elseif ($id === false || $id === null || $id <= 0) {
            $this->flash('Permintaan tidak valid.', true);
        } elseif (!$this->milikSaya($this->model->ambilById($id))) {
            $this->flash('Catatan tidak ditemukan.', true);
        } else {
            $this->model->hapus($id);
            $this->flash('Catatan berhasil dihapus.', false);
        }

        $this->redirectKeCatatanSaya();
    }
}