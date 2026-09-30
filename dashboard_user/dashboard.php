<?php
declare(strict_types=1);

const APP_NAME = 'CatatIn';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/*
 * Data contoh katalog (statis, belum database).
 * Urutan kolom: id, judul, matkul, jurusan, kategori, penulis, upvote, tanggal
 */
function katalog(): array
{
    $baris = [
        [1,  'Rangkuman Struktur Data — Pohon & Graf', 'Struktur Data',         'Informatika',      'Rangkuman',  'Nazma F.',    128, '2026-09-25'],
        [2,  'Kumpulan Soal UAS Basis Data 2025',      'Basis Data',            'Sistem Informasi', 'Soal Ujian', 'sara wijayanto',       96, '2026-09-22'],
        [3,  'Modul Praktikum Rangkaian Digital',      'Rangkaian Digital',     'Elektro',          'Modul',      'Nadia omara',     54, '2026-09-20'],
        [4,  'Rangkuman Pemrograman Web — Sesi 1–7',   'Pemrograman Web',       'Informatika',      'Rangkuman',  'Afifatul M.',  41, '2026-09-27'],
        [5,  'Rangkuman Struktur Data',                'Struktur Data',         'Informatika',      'Rangkuman',  'Rizky A.',     37, '2026-09-10'],
        [6,  'Modul Praktikum HTML/CSS',               'Pemrograman Web',       'Informatika',      'Modul',      'Dimas P.',     63, '2026-09-12'],
    ];

    $hasil = [];
    foreach ($baris as [$id, $judul, $matkul, $jurusan, $kategori, $penulis, $upvote, $tanggal]) {
        $hasil[] = compact('id', 'judul', 'matkul', 'jurusan', 'kategori', 'penulis', 'upvote', 'tanggal');
    }
    return $hasil;
}

$daftarJurusan  = ['Informatika', 'Elektro', 'Sistem Informasi'];
$daftarKategori = ['Rangkuman', 'Soal Ujian', 'Modul'];

$catatan = katalog();
usort($catatan, static fn(array $a, array $b): int => strcmp($b['tanggal'], $a['tanggal']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard  <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="app">
    <header class="topbar">
        <button type="button" class="hamburger" id="tombol-menu" aria-label="Buka menu"
                aria-expanded="false" aria-controls="sidebar">
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
                <li><a href="#">Unggah Catatan</a></li>
                <li><a href="#">Catatan Saya</a></li>
                <li><a href="../playlist/playlist.php">Koleksi Belajar</a></li>
                <li><a href="#">Tugas Belajar</a></li>
                <li><a href="#">Profil</a></li>
                <li><a href="auth/login.php">Keluar</a></li>
            </ul>
        </nav>
    </aside>

    <main class="konten">
        <div class="judul-baris">
            <h1>Jelajahi catatan</h1>
            <div class="avatar" title="Akun saya"><?= e(PENGGUNA_INISIAL) ?></div>
        </div>

        <form class="bar-cari" role="search" id="form-cari">
            <label class="kolom-cari">
                <span aria-hidden="true">🔍</span>
                <span class="sr-only">Cari catatan</span>
                <input type="search" id="cari" placeholder="Cari judul, matkul, atau penyusun...">
            </label>
            <label class="pilih-urut">
                <span class="sr-only">Urutkan</span>
                <select id="urut">
                    <option value="terbaru">↓ Terbaru</option>
                    <option value="populer">↓ Terpopuler</option>
                </select>
            </label>
        </form>

        <div class="chip-baris" aria-label="Filter catatan">
            <label class="pilih-jurusan">
                <span class="sr-only">Jurusan</span>
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
                         data-kategori="<?= e($n['kategori']) ?>"
                         data-tanggal="<?= e($n['tanggal']) ?>"
                         data-cari="<?= e(mb_strtolower($n['judul'] . ' ' . $n['matkul'] . ' ' . $n['penulis'])) ?>">
                    <span class="lencana"><?= e($n['jurusan']) ?></span>
                    <h2><?= e($n['judul']) ?></h2>
                    <p class="meta">oleh <?= e($n['penulis']) ?> · <?= e($n['kategori']) ?></p>
                    <div class="kartu-bawah">
                        <button type="button" class="upvote" data-dasar="<?= (int) $n['upvote'] ?>" aria-pressed="false">
                            <span aria-hidden="true">▲</span>
                            <span class="jumlah"><?= (int) $n['upvote'] ?></span> upvote
                        </button>
                        <a class="detail" href="#">Lihat detail</a>
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
