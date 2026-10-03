<?php
declare(strict_types=1);
session_start();

const APP_NAME = 'CatatIn';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/* Data simulasi disimpan di session (sama dengan upload.php) */
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

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

ambilCatatan();

// Proses hapus (POST + CSRF), lalu redirect agar tidak terkirim ulang saat refresh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    $idHapus = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        $_SESSION['flash'] = ['pesan' => 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.', 'galat' => true];
    } elseif ($idHapus === false || $idHapus === null) {
        $_SESSION['flash'] = ['pesan' => 'Permintaan tidak valid.', 'galat' => true];
    } else {
        $sebelum = count($_SESSION['catatan']);
        $_SESSION['catatan'] = array_values(array_filter(
            $_SESSION['catatan'],
            static fn(array $c): bool => $c['id'] !== $idHapus
        ));
        $_SESSION['flash'] = count($_SESSION['catatan']) < $sebelum
            ? ['pesan' => 'Catatan berhasil dihapus.', 'galat' => false]
            : ['pesan' => 'Catatan tidak ditemukan.', 'galat' => true];
    }
    header('Location: catatan_saya.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$catatanSaya = $_SESSION['catatan'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catatan Saya — <?= APP_NAME ?></title>
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
                <li><a href="upload.php">Unggah Catatan</a></li>
                <li><a href="catatan_saya.php" aria-current="page">Catatan Saya</a></li>
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
            <h1>Catatan saya</h1>
            <div class="avatar" title="Akun saya"><?= e(PENGGUNA_INISIAL) ?></div>
        </div>

        <?php if ($flash): ?>
            <div class="toast<?= $flash['galat'] ? ' toast-error' : '' ?>" role="status" data-auto>
                <?= e($flash['pesan']) ?>
            </div>
        <?php endif; ?>

        <div class="tabel-container">
            <table class="tabel-catatan">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Upvote</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($catatanSaya === []): ?>
                        <tr>
                            <td colspan="4">
                                Belum ada catatan. <a href="upload.php">Unggah catatan pertamamu</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($catatanSaya as $item): ?>
                        <tr>
                            <td class="td-judul"><?= e($item['judul']) ?></td>
                            <td><?= e($item['tipe']) ?></td>
                            <td><?= (int) $item['upvote'] ?></td>
                            <td class="td-aksi">
                                <a href="upload.php?edit=<?= (int) $item['id'] ?>" class="link-edit">Edit</a>
                                <form action="catatan_saya.php" method="POST" class="form-hapus"
                                      onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="link-hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="dashboard.js"></script>
</body>
</html>