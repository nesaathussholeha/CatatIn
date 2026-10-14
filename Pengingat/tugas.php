<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

/* ---------- Data awal (disimpan di session, belum pakai database) ---------- */
if (!isset($_SESSION['tugas'])) {
    $_SESSION['tugas'] = [
        ['id' => 1, 'judul' => 'Review Catatan Algoritma Bab 3', 'deadline' => '2026-10-02', 'selesai' => false],
        ['id' => 2, 'judul' => 'Baca Modul Basis Data Bab 5',    'deadline' => '2026-10-08', 'selesai' => false],
        ['id' => 3, 'judul' => 'Rangkum Materi Jaringan Bab 2',  'deadline' => '2026-09-25', 'selesai' => true],
    ];
    $_SESSION['next_id'] = 4;
}

/* ---------- Proses aksi (tambah, edit, hapus, toggle) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id   = (int)($_POST['id'] ?? 0);

    if ($aksi === 'tambah') {
        $judul    = trim($_POST['judul'] ?? '');
        $deadline = $_POST['deadline'] ?? '';
        if ($judul !== '' && $deadline !== '') {
            $_SESSION['tugas'][] = [
                'id'       => $_SESSION['next_id']++,
                'judul'    => $judul,
                'deadline' => $deadline,
                'selesai'  => false,
            ];
        }
    } elseif ($aksi === 'edit') {
        foreach ($_SESSION['tugas'] as &$t) {
            if ($t['id'] === $id) {
                $judul    = trim($_POST['judul'] ?? '');
                $deadline = $_POST['deadline'] ?? '';
                if ($judul !== '' && $deadline !== '') {
                    $t['judul']    = $judul;
                    $t['deadline'] = $deadline;
                }
            }
        }
        unset($t);
    } elseif ($aksi === 'hapus') {
        $_SESSION['tugas'] = array_values(array_filter(
            $_SESSION['tugas'],
            fn($t) => $t['id'] !== $id
        ));
    } elseif ($aksi === 'toggle') {
        foreach ($_SESSION['tugas'] as &$t) {
            if ($t['id'] === $id) {
                $t['selesai'] = !$t['selesai'];
            }
        }
        unset($t);
    }

    $qs = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: tugas.php' . ($qs !== '' ? '?' . $qs : ''));
    exit;
}

/* ---------- Helper ---------- */
function e($teks) {
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}

function formatTanggal($tanggal) {
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $ts = strtotime($tanggal);
    return date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

// Return [teks, kelas css] untuk badge status
function statusTugas($t) {
    if ($t['selesai']) {
        return ['Selesai', 'badge-selesai'];
    }
    $hariIni  = strtotime(date('Y-m-d'));
    $deadline = strtotime($t['deadline']);
    $sisaHari = ($deadline - $hariIni) / 86400;

    if ($sisaHari < 0) {
        return ['Terlambat', 'badge-terlambat'];
    }
    if ($sisaHari <= 3) {
        return ['Mendekati deadline', 'badge-dekat'];
    }
    return ['Belum jatuh tempo', 'badge-aman'];
}

/* ---------- Filter status & urutan deadline (Read) ---------- */
$filter = $_GET['status'] ?? 'semua';
if (!in_array($filter, ['semua', 'belum', 'selesai'], true)) {
    $filter = 'semua';
}
$urut = $_GET['urut'] ?? 'terdekat';
if (!in_array($urut, ['terdekat', 'terjauh'], true)) {
    $urut = 'terdekat';
}

$daftar = array_values(array_filter($_SESSION['tugas'], function ($t) use ($filter) {
    if ($filter === 'belum')    return !$t['selesai'];
    if ($filter === 'selesai')  return $t['selesai'];
    return true;
}));

usort($daftar, function ($a, $b) use ($urut) {
    $hasil = strtotime($a['deadline']) <=> strtotime($b['deadline']);
    return $urut === 'terjauh' ? -$hasil : $hasil;
});

/* ---------- Pengaturan layout (sidebar baru) ---------- */
$page_title   = "Tugas Belajar";
$active_menu  = "tugas"; // 'dashboard', 'unggah', 'catatan_saya', 'koleksi', 'tugas', 'profil'

$user_name    = "Username";
$user_role    = "Role";
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title); ?> - CatatIn</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- CSS (sidebar baru + halaman tugas) dalam satu file -->
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>CatatIn</span>
                    </div>
                    <button type="button" class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="../dashboard_user/dashboard.php" class="nav-link <?= ($active_menu == 'dashboard') ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="upload.php" class="nav-link <?= ($active_menu == 'unggah') ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon> Unggah Catatan
                        </a>
                    </li>
                    <li>
                        <a href="catatan_saya.php" class="nav-link <?= ($active_menu == 'catatan_saya') ? 'active' : ''; ?>">
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
                         <a href="../profile.php" class="nav-link <?= ($active_menu == 'profil') ? 'active' : ''; ?>">
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
                    <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka Menu">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= e($page_title); ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name); ?></span>
                        <span class="user-role"><?= e($user_role); ?></span>
                    </div>
                    <div class="avatar"><?= e($user_initial); ?></div>
                </div>
            </header>

            <!-- Area Konten Utama -->
            <main class="content-area">

                <header class="konten-header">
    <h2 class="judul-halaman">Tugas belajar saya</h2>
</header>

                <div class="filter-bar">
                    <div class="filter-tab">
                        <a href="?status=semua&urut=<?= e($urut) ?>" class="<?= $filter === 'semua' ? 'aktif' : '' ?>">Semua</a>
                        <a href="?status=belum&urut=<?= e($urut) ?>" class="<?= $filter === 'belum' ? 'aktif' : '' ?>">Belum selesai</a>
                        <a href="?status=selesai&urut=<?= e($urut) ?>" class="<?= $filter === 'selesai' ? 'aktif' : '' ?>">Selesai</a>
                    </div>
                    <form method="get" class="form-urut">
                        <input type="hidden" name="status" value="<?= e($filter) ?>">
                        <label for="pilihUrut">Urutkan:</label>
                        <select name="urut" id="pilihUrut" class="pilih-urut">
                            <option value="terdekat" <?= $urut === 'terdekat' ? 'selected' : '' ?>>Deadline terdekat</option>
                            <option value="terjauh" <?= $urut === 'terjauh' ? 'selected' : '' ?>>Deadline terjauh</option>
                        </select>
                    </form>
                </div>

                <section class="daftar-tugas">
                    <?php if (empty($daftar)): ?>
                        <div class="kosong">Belum ada tugas di sini. Klik "+ Tambah tugas" buat mulai ya!</div>
                    <?php endif; ?>

                    <?php foreach ($daftar as $t):
                        [$teksStatus, $kelasStatus] = statusTugas($t);
                    ?>
                        <article class="kartu-tugas <?= $t['selesai'] ? 'selesai' : '' ?>">
                            <div class="kartu-kiri">
                                <form method="post" class="form-toggle">
                                    <input type="hidden" name="aksi" value="toggle">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <input type="checkbox" class="cek-tugas" <?= $t['selesai'] ? 'checked' : '' ?>>
                                </form>
                                <div>
                                    <div class="judul-tugas"><?= e($t['judul']) ?></div>
                                    <div class="info-tugas">
                                        <?= $t['selesai'] ? 'Selesai' : 'Deadline: ' . e(formatTanggal($t['deadline'])) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="kartu-kanan">
                                <span class="badge <?= $kelasStatus ?>"><?= $teksStatus ?></span>

                                <?php if (!$t['selesai']): ?>
                                    <button type="button" class="aksi aksi-edit btn-edit"
                                            data-id="<?= $t['id'] ?>"
                                            data-judul="<?= e($t['judul']) ?>"
                                            data-deadline="<?= e($t['deadline']) ?>">Edit</button>
                                <?php endif; ?>

                                <form method="post" class="form-hapus">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="aksi aksi-hapus">Hapus</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
                </section>

                <div class="area-tambah">
                    <button type="button" class="btn-utama" id="btnTambah" aria-label="Tambah tugas">
                        + Tambah tugas
                    </button>
                </div>

            </main>

            </main>
        </div>
    </div>

    <!-- ===== Modal tambah / edit tugas ===== -->
    <div class="modal-latar" id="modalTugas" aria-hidden="true">
        <div class="modal" role="dialog" aria-labelledby="modalJudul">
            <h2 id="modalJudul">Tambah tugas</h2>
            <form method="post" id="formTugas">
                <input type="hidden" name="aksi" id="inputAksi" value="tambah">
                <input type="hidden" name="id" id="inputId" value="">

                <label for="inputJudul">Nama tugas</label>
                <input type="text" name="judul" id="inputJudul" placeholder="Contoh: Rangkum Bab 4 Jaringan" required>

                <label for="inputDeadline">Deadline</label>
                <input type="date" name="deadline" id="inputDeadline" required>

                <div class="modal-tombol">
                    <button type="button" class="btn-batal" id="btnBatal">Batal</button>
                    <button type="submit" class="btn-utama">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js?v=<?= filemtime(__DIR__ . '/script.js') ?>"></script>
</body>

</html>