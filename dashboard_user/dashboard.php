<?php

declare(strict_types=1);

session_start();

const APP_NAME = 'CatatIn';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

$daftarJurusan  = ['Informatika', 'Elektro', 'Sistem Informasi', 'Teknik Komputer'];
$daftarKategori = ['Rangkuman', 'Soal Ujian', 'Modul'];

// Jika session belum ada, isi data awal
if (!isset($_SESSION['semua_catatan'])) {
    $_SESSION['semua_catatan'] = [
        ['id' => 1, 'judul' => 'Rangkuman Struktur Data', 'matkul' => 'Struktur Data', 'jurusan' => 'Informatika', 'tipe' => 'Rangkuman', 'penulis' => 'SF', 'upvote' => 128, 'tanggal' => '2026-09-25', 'tautan' => 'https://drive.google.com', 'is_saya' => true],
        ['id' => 2, 'judul' => 'Latihan Soal Kalkulus II', 'matkul' => 'Kalkulus II', 'jurusan' => 'Informatika', 'tipe' => 'Soal Ujian', 'penulis' => 'SF', 'upvote' => 37, 'tanggal' => '2026-09-18', 'tautan' => 'https://drive.google.com', 'is_saya' => true],
        ['id' => 3, 'judul' => 'Modul Praktikum Jaringan', 'matkul' => 'Jaringan Komputer', 'jurusan' => 'Teknik Komputer', 'tipe' => 'Modul', 'penulis' => 'SF', 'upvote' => 19, 'tanggal' => '2026-09-15', 'tautan' => 'https://drive.google.com', 'is_saya' => true],
        ['id' => 4, 'judul' => 'Kumpulan Soal UAS Basis Data', 'matkul' => 'Basis Data', 'jurusan' => 'Sistem Informasi', 'tipe' => 'Soal Ujian', 'penulis' => 'Sara Wijayanto', 'upvote' => 96, 'tanggal' => '2026-09-22', 'tautan' => 'https://drive.google.com', 'is_saya' => false],
    ];
}

$catatan = $_SESSION['semua_catatan'];
usort($catatan, static fn(array $a, array $b): int => strcmp($b['tanggal'], $a['tanggal']));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="app">
        <header class="topbar">
            <button type="button" class="hamburger" id="tombol-menu" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>
            <a class="logo-atas" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
        </header>
        <div class="overlay" id="overlay"></div>

        <aside class="sidebar" id="sidebar">
            <button type="button" class="tutup-menu" id="tutup-menu" aria-label="Tutup menu">✕</button>
            <a class="logo" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
            <nav aria-label="Menu utama">
                <ul class="menu">
                    <li><a href="dashboard.php" aria-current="page">Dashboard</a></li>
                    <li><a href="upload.php">Unggah Catatan</a></li>
                    <li><a href="catatan_saya.php">Catatan Saya</a></li>
                    <li><a href="../playlist/playlist.php">Koleksi Belajar</a></li>
                    <li><a href="#">Tugas Belajar</a></li>
                    <li><a href="#">Profil</a></li>
                </ul>
            </nav>
            <div class="sidebar-bawah">
                <a href="../auth/login.php" class="menu-keluar">Keluar</a>
            </div>
        </aside>

        <main class="konten">
            <div class="judul-baris">
                <h1>Jelajahi catatan</h1>
                <div class="avatar" title="Akun saya"><?= e(PENGGUNA_INISIAL) ?></div>
            </div>

            <form class="bar-cari" role="search" id="form-cari">
                <label class="kolom-cari">
                    <span aria-hidden="true">🔍</span>
                    <input type="search" id="cari" placeholder="Cari judul, matkul, atau penyusun...">
                </label>
                <label class="pilih-urut">
                    <select id="urut">
                        <option value="terbaru">↓ Terbaru</option>
                        <option value="populer">↓ Terpopuler</option>
                    </select>
                </label>
            </form>

            <div class="chip-baris">
                <label class="pilih-jurusan">
                    <select id="jurusan">
                        <option value="">Semua jurusan</option>
                        <?php foreach ($daftarJurusan as $j): ?>
                            <option value="<?= e($j) ?>"><?= e($j) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php foreach ($daftarKategori as $k): ?>
                    <button type="button" class="chip" data-kategori="<?= e($k) ?>" aria-pressed="false"><?= e($k) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="grid-kartu" id="grid">
                <?php foreach ($catatan as $n): ?>
                    <article class="kartu"
                        data-jurusan="<?= e($n['jurusan']) ?>"
                        data-kategori="<?= e($n['tipe']) ?>"
                        data-tanggal="<?= e($n['tanggal']) ?>"
                        data-cari="<?= e(mb_strtolower($n['judul'] . ' ' . $n['matkul'] . ' ' . $n['penulis'])) ?>">
                        <span class="lencana"><?= e($n['jurusan']) ?></span>
                        <h2><?= e($n['judul']) ?></h2>
                        <p class="meta">oleh <?= e($n['penulis']) ?> · <?= e($n['tipe']) ?></p>
                        <div class="kartu-bawah">
                            <button type="button" class="upvote" data-dasar="<?= (int) $n['upvote'] ?>" aria-pressed="false">
                                <span aria-hidden="true">▲</span>
                                <span class="jumlah"><?= (int) $n['upvote'] ?></span> upvote
                            </button>
                            <a class="detail" href="<?= e($n['tautan']) ?>" target="_blank" rel="noopener">Lihat detail</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="kosong" id="kosong" hidden>
                <p><strong>Tidak ada catatan yang cocok.</strong></p>
                <p>Coba kata kunci lain atau ubah filter.</p>
                <button type="button" class="tombol" id="reset">Reset pencarian</button>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>

</html>