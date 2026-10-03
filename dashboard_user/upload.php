<?php
declare(strict_types=1);
session_start();

const APP_NAME = 'CatatIn';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/* Data simulasi disimpan di session (sementara, sebelum ada database) */
function ambilCatatan(): array
{
    if (!isset($_SESSION['catatan'])) {
        $_SESSION['catatan'] = [
            ['id' => 1, 'judul' => 'Rangkuman Struktur Data', 'matkul' => 'Struktur Data', 'jurusan' => 'Informatika', 'tipe' => 'Rangkuman', 'deskripsi' => '', 'tautan' => '', 'anonim' => false, 'upvote' => 128],
            ['id' => 2, 'judul' => 'Latihan Soal Kalkulus II', 'matkul' => 'Kalkulus II', 'jurusan' => 'Informatika', 'tipe' => 'Soal Ujian', 'deskripsi' => '', 'tautan' => '', 'anonim' => false, 'upvote' => 37],
            ['id' => 3, 'judul' => 'Modul Praktikum Jaringan', 'matkul' => 'Jaringan Komputer', 'jurusan' => 'Teknik Komputer', 'tipe' => 'Modul', 'deskripsi' => '', 'tautan' => '', 'anonim' => false, 'upvote' => 19],
        ];
    }
    return $_SESSION['catatan'];
}

function ambilPost(string $kunci): string
{
    $nilai = $_POST[$kunci] ?? '';
    return is_string($nilai) ? trim($nilai) : '';
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$daftarJurusan  = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Elektro'];
$daftarKategori = ['Rangkuman', 'Soal Ujian', 'Modul'];

ambilCatatan();

$galat = [];
$idForm = 0;
$input = [
    'judul' => '', 'matkul' => '', 'jurusan' => $daftarJurusan[0],
    'kategori' => $daftarKategori[0], 'deskripsi' => '', 'tautan' => '', 'is_anonim' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idForm = (int) ambilPost('id');
    $input = [
        'judul'     => ambilPost('judul'),
        'matkul'    => ambilPost('matkul'),
        'jurusan'   => ambilPost('jurusan'),
        'kategori'  => ambilPost('kategori'),
        'deskripsi' => ambilPost('deskripsi'),
        'tautan'    => ambilPost('tautan'),
        'is_anonim' => ambilPost('is_anonim') === '1',
    ];

    if (!hash_equals($_SESSION['csrf'], ambilPost('csrf'))) {
        http_response_code(400);
        $galat[] = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    if ($input['judul'] === '' || mb_strlen($input['judul']) > 150) {
        $galat[] = 'Judul wajib diisi (maksimal 150 karakter).';
    }
    if ($input['matkul'] === '' || mb_strlen($input['matkul']) > 100) {
        $galat[] = 'Mata kuliah wajib diisi (maksimal 100 karakter).';
    }
    if (!in_array($input['jurusan'], $daftarJurusan, true)) {
        $galat[] = 'Jurusan tidak valid.';
    }
    if (!in_array($input['kategori'], $daftarKategori, true)) {
        $galat[] = 'Tipe berkas tidak valid.';
    }
    if ($input['deskripsi'] === '' || mb_strlen($input['deskripsi']) > 255) {
        $galat[] = 'Deskripsi wajib diisi (maksimal 255 karakter).';
    }

    // Tautan: tambahkan https:// jika belum ada, lalu validasi
    $tautan = $input['tautan'];
    if ($tautan !== '' && !preg_match('~^https?://~i', $tautan)) {
        $tautan = 'https://' . $tautan;
    }
    $host = $tautan !== '' ? parse_url($tautan, PHP_URL_HOST) : null;
    if ($tautan === '' || mb_strlen($tautan) > 500
        || filter_var($tautan, FILTER_VALIDATE_URL) === false
        || !is_string($host) || strpos($host, '.') === false) {
        $galat[] = 'Tautan berkas tidak valid.';
    } else {
        $input['tautan'] = $tautan;
    }

    if ($galat === []) {
        if ($idForm > 0) {
            $ketemu = false;
            foreach ($_SESSION['catatan'] as &$c) {
                if ($c['id'] === $idForm) {
                    $c['judul']     = $input['judul'];
                    $c['matkul']    = $input['matkul'];
                    $c['jurusan']   = $input['jurusan'];
                    $c['tipe']      = $input['kategori'];
                    $c['deskripsi'] = $input['deskripsi'];
                    $c['tautan']    = $input['tautan'];
                    $c['anonim']    = $input['is_anonim'];
                    $ketemu = true;
                    break;
                }
            }
            unset($c);
            $_SESSION['flash'] = $ketemu
                ? ['pesan' => 'Catatan berhasil diperbarui.', 'galat' => false]
                : ['pesan' => 'Catatan tidak ditemukan.', 'galat' => true];
        } else {
            $idBaru = $_SESSION['catatan'] === [] ? 1 : max(array_column($_SESSION['catatan'], 'id')) + 1;
            $_SESSION['catatan'][] = [
                'id'        => $idBaru,
                'judul'     => $input['judul'],
                'matkul'    => $input['matkul'],
                'jurusan'   => $input['jurusan'],
                'tipe'      => $input['kategori'],
                'deskripsi' => $input['deskripsi'],
                'tautan'    => $input['tautan'],
                'anonim'    => $input['is_anonim'],
                'upvote'    => 0,
            ];
            $_SESSION['flash'] = ['pesan' => 'Catatan berhasil disimpan.', 'galat' => false];
        }
        header('Location: catatan_saya.php');
        exit;
    }
} elseif (isset($_GET['edit'])) {
    // Mode edit: isi form dengan data lama
    $idEdit = (int) $_GET['edit'];
    foreach ($_SESSION['catatan'] as $c) {
        if ($c['id'] === $idEdit) {
            $idForm = $idEdit;
            $input = [
                'judul' => $c['judul'], 'matkul' => $c['matkul'], 'jurusan' => $c['jurusan'],
                'kategori' => $c['tipe'], 'deskripsi' => $c['deskripsi'], 'tautan' => $c['tautan'],
                'is_anonim' => (bool) $c['anonim'],
            ];
            break;
        }
    }
    if ($idForm === 0) {
        $galat[] = 'Catatan yang ingin diedit tidak ditemukan.';
    }
}

$modeEdit = $idForm > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $modeEdit ? 'Edit Catatan' : 'Unggah Catatan Baru' ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="app">
    <!-- Header Topbar Mobile -->
    <header class="topbar">
        <button type="button" class="hamburger" id="tombol-menu" aria-label="Buka menu"
                aria-expanded="false" aria-controls="sidebar">
            <span></span><span></span><span></span>
        </button>
        <a class="logo-atas" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
    </header>
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <button type="button" class="tutup-menu" id="tutup-menu" aria-label="Tutup menu">✕</button>
        <a class="logo" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
        <nav aria-label="Menu utama">
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="upload.php" aria-current="page">Unggah Catatan</a></li>
                <li><a href="catatan_saya.php">Catatan Saya</a></li>
                <li><a href="#">Profil</a></li>
            </ul>
        </nav>
        <div class="sidebar-bawah">
            <a href="../auth/login.php" class="menu-keluar">Keluar</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="konten">
        <div class="top-bar-desktop">
            <h1><?= $modeEdit ? 'Edit catatan' : 'Unggah catatan' ?></h1>
            <div class="avatar" title="Akun saya"><?= e(PENGGUNA_INISIAL) ?></div>
        </div>

        <div class="card-form">
            <?php if ($galat !== []): ?>
                <div class="alert-galat" role="alert">
                    <ul>
                        <?php foreach ($galat as $g): ?>
                            <li><?= e($g) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="upload.php" method="POST">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <input type="hidden" name="id" value="<?= $idForm ?>">

                <div class="form-group">
                    <label for="judul">Judul catatan</label>
                    <input type="text" id="judul" name="judul" maxlength="150"
                           value="<?= e($input['judul']) ?>"
                           placeholder="Rangkuman Aljabar Linear Bab 3" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="matkul">Mata kuliah</label>
                        <input type="text" id="matkul" name="matkul" maxlength="100"
                               value="<?= e($input['matkul']) ?>"
                               placeholder="Aljabar Linear" required>
                    </div>
                    <div class="form-group">
                        <label for="jurusan">Jurusan</label>
                        <select id="jurusan" name="jurusan" required>
                            <?php foreach ($daftarJurusan as $j): ?>
                                <option value="<?= e($j) ?>" <?= $input['jurusan'] === $j ? 'selected' : '' ?>><?= e($j) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tipe berkas</label>
                    <div class="pill-group">
                        <?php foreach ($daftarKategori as $k): ?>
                            <label class="pill-btn">
                                <input type="radio" name="kategori" value="<?= e($k) ?>" <?= $input['kategori'] === $k ? 'checked' : '' ?>>
                                <span><?= e($k) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi singkat</label>
                    <input type="text" id="deskripsi" name="deskripsi" maxlength="255"
                           value="<?= e($input['deskripsi']) ?>"
                           placeholder="Mencakup transformasi linear dan nilai eigen..." required>
                </div>

                <div class="form-group">
                    <label for="tautan">Tautan berkas</label>
                    <input type="text" id="tautan" name="tautan" maxlength="500"
                           value="<?= e($input['tautan']) ?>"
                           placeholder="drive.google.com/..." required>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="is_anonim" value="1" <?= $input['is_anonim'] ? 'checked' : '' ?>>
                        Unggah sebagai anonim
                    </label>
                </div>

                <button type="submit" class="btn-primary"><?= $modeEdit ? 'Perbarui catatan' : 'Simpan catatan' ?></button>
            </form>
        </div>
    </main>
</div>

<script src="dashboard.js"></script>
</body>
</html>