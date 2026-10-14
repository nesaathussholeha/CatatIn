<?php
declare(strict_types=1);

// 1. Inisialisasi Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Proteksi Auth Guard (User Wajib Login)
if (empty($_SESSION['user_id'])) {
    $_SESSION['flash'] = [
        'pesan' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.',
        'tipe'  => 'error'
    ];
    header('Location: ../auth/login.php');
    exit;
}

// 3. Proteksi Anti-Tembak URL Langsung
// Mengecek apakah halaman dibuka dari navigasi internal (Dashboard/menu)
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$domain_asal = parse_url($referer, PHP_URL_HOST);
$domain_saat_ini = $_SERVER['HTTP_HOST'] ?? '';

// Jika tidak ada Referer (diketik/ditembak langsung) atau berasal dari domain luar
if (empty($referer) || $domain_asal !== $domain_saat_ini) {
    header('Location: ../dashboard_user/dashboard.php');
    exit;
}


const APP_NAME = 'CatatIn';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/* ---------- Pengaturan layout (dipakai sidebar & header) ---------- */
$page_title   = 'Jelajahi Catatan';
$active_menu  = 'dashboard'; // 'dashboard', 'unggah', 'catatan_saya', 'koleksi', 'tugas', 'profil'

$user_name    = 'Username';
$user_role    = 'Role';
$user_initial = strtoupper(substr($user_name, 0, 1));

/*
 * Data contoh katalog (statis, belum database).
 * Urutan kolom: id, judul, matkul, jurusan, kategori, penulis, upvote, tanggal
 */
function katalog(): array
{
    $baris = [
        [1,  'Rangkuman Struktur Data — Pohon & Graf', 'Struktur Data',     'Informatika',      'Rangkuman',  'Nazma F.',       128, '2026-09-25'],
        [2,  'Kumpulan Soal UAS Basis Data 2025',      'Basis Data',        'Sistem Informasi', 'Soal Ujian', 'sara wijayanto',  96, '2026-09-22'],
        [3,  'Modul Praktikum Rangkaian Digital',      'Rangkaian Digital', 'Elektro',          'Modul',      'Nadia omara',     54, '2026-09-20'],
        [4,  'Rangkuman Pemrograman Web — Sesi 1–7',   'Pemrograman Web',   'Informatika',      'Rangkuman',  'Afifatul M.',     41, '2026-09-27'],
        [5,  'Rangkuman Struktur Data',                'Struktur Data',     'Informatika',      'Rangkuman',  'Rizky A.',        37, '2026-09-10'],
        [6,  'Modul Praktikum HTML/CSS',               'Pemrograman Web',   'Informatika',      'Modul',      'Dimas P.',        63, '2026-09-12'],
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - <?= APP_NAME ?></title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Layout bersama, lalu gaya khusus dashboard -->
    <link rel="stylesheet" href="../layout_user/style.css">
    <link rel="stylesheet" href="dashboard.css?v=<?= (int) @filemtime(__DIR__ . '/dashboard.css') ?>">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
                <!-- Header Sidebar + Tombol Close (X) -->
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>CatatIn</span>
                    </div>
                    <button class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="dashboard.php" class="nav-link <?= ($active_menu == 'dashboard') ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="../upload/upload.php" class="nav-link <?= ($active_menu == 'unggah') ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon> Unggah Catatan
                        </a>
                    </li>
                    <li>
                        <a href="../catatan saya/catatan_saya.php" class="nav-link <?= ($active_menu == 'catatan_saya') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon> Catatan Saya
                        </a>
                    </li>
                    <li>
                        <a href="../playlist/playlist.php" class="nav-link <?= ($active_menu == 'koleksi') ? 'active' : ''; ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon> Koleksi
                        </a>
                    </li>
                    <li>
                        <a href="../Pengingat/tugas.php" class="nav-link <?= ($active_menu == 'tugas') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon> Tugas
                        </a>
                    </li>
                    <li>
                        <a href="../profile/profile.php" class="nav-link <?= ($active_menu == 'profil') ? 'active' : ''; ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon> Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="../logout.php" class="nav-link logout">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon> Keluar
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Buka Menu">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= e($page_title) ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name) ?></span>
                        <span class="user-role"><?= e($user_role) ?></span>
                    </div>
                    <div class="avatar"><?= e($user_initial) ?></div>
                </div>
            </header>

            <!-- Area Konten Utama -->
            <main class="content-area">
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
                                <a class="detail" href="../report/report_user.php">Lihat detail</a>
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
    </div>

    <script src="../layout_user/script.js"></script>
    <script src="dashboard.js?v=<?= (int) @filemtime(__DIR__ . '/dashboard.js') ?>"></script>
</body>

</html>