<?php
declare(strict_types=1);

const APP_NAME = 'Notes Hub';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

// Data simulasi catatan pengguna
$catatanSaya = [
    ['id' => 1, 'judul' => 'Rangkuman Struktur Data', 'tipe' => 'Rangkuman', 'upvote' => 128],
    ['id' => 2, 'judul' => 'Latihan Soal Kalkulus II', 'tipe' => 'Soal Ujian', 'upvote' => 37],
    ['id' => 3, 'judul' => 'Modul Praktikum Jaringan', 'tipe' => 'Modul', 'upvote' => 19],
];
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
        <button type="button" class="hamburger" id="tombol-menu" aria-label="Buka menu">
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
                <li><a href="catatan-saya.php" aria-current="page">Catatan Saya</a></li>
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
                    <?php foreach ($catatanSaya as $item): ?>
                        <tr>
                            <td class="td-judul"><?= e($item['judul']) ?></td>
                            <td><?= e($item['tipe']) ?></td>
                            <td><?= (int) $item['upvote'] ?></td>
                            <td class="td-aksi">
                                <a href="upload.php?edit=<?= $item['id'] ?>" class="link-edit">Edit</a>
                                <a href="proses-hapus.php?id=<?= $item['id'] ?>" class="link-hapus" onclick="return confirm('Yakin ingin menghapus catatan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>