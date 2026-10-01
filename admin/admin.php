

<?php
/**
 * admin.php — Catatin Admin Panel
 * Halaman: Dashboard, Kelola Catatan, Kelola Pengguna, Keluar (dipilih lewat ?page=...)
 * CSS  -> admin.css
 * JS   -> admin.js
 *
 * CRUD:
 *  - Catatan  : Tambah (C), Lihat + Cari (R), Edit (U), Hapus (D)
 *  - Pengguna : Tambah (C), Lihat + Cari (R), Edit + Blokir/Buka Blokir (U), Hapus (D)
 *
 * Data sementara disimpan di $_SESSION (belum pakai database).
 */

session_start();

// ==========================================================
// LOGOUT (?page=logout)
// ==========================================================
if (($_GET['page'] ?? '') === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: login.php'); // ganti sesuai halaman login kamu
    exit;
}

// ==========================================================
// HELPER
// ==========================================================
function e($nilai)
{
    return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8');
}

function flash($tipe, $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function panjang($teks)
{
    return mb_strlen($teks, 'UTF-8');
}

function formatAngka($n)
{
    if ($n >= 1000) {
        return rtrim(rtrim(number_format($n / 1000, 1, '.', ''), '0'), '.') . 'k';
    }
    return (string)$n;
}

function nimSudahDipakai($nim, $kecualiId)
{
    foreach ($_SESSION['admin_pengguna'] as $p) {
        if (strcasecmp($p['nim'], $nim) === 0 && $p['id'] !== $kecualiId) {
            return true;
        }
    }
    return false;
}

// ==========================================================
// DATA AWAL (dummy, disimpan di session)
// ==========================================================
if (!isset($_SESSION['admin_catatan']) || ($_SESSION['admin_versi'] ?? 0) < 2) {
    $_SESSION['admin_catatan'] = [
        ['id' => 1, 'judul' => 'Ringkasan Struktur Data — Binary Tree',  'penulis' => 'Sarah Faradila',  'jurusan' => 'Informatika',       'upvote' => 128, 'status' => 'terbit',
         'alasan_laporan' => '',
         'isi' => "Binary tree adalah struktur data berbentuk pohon di mana setiap node punya maksimal dua anak: kiri dan kanan.\n\nTraversal ada tiga: preorder (akar-kiri-kanan), inorder (kiri-akar-kanan), dan postorder (kiri-kanan-akar).\n\nContoh pemakaian: pencarian data (binary search tree) dan ekspresi matematika."],
        ['id' => 2, 'judul' => 'Rangkuman Basis Data Relasional',         'penulis' => 'Nazma Fairuz M.', 'jurusan' => 'Sistem Informasi',   'upvote' => 94,  'status' => 'terbit',
         'alasan_laporan' => '',
         'isi' => "Basis data relasional menyimpan data dalam tabel yang punya baris dan kolom.\n\nKonsep penting: primary key, foreign key, dan normalisasi (1NF sampai 3NF) untuk mengurangi data ganda."],
        ['id' => 3, 'judul' => 'Catatan Kalkulus II — Integral Lipat',    'penulis' => 'Bagas Wicaksono', 'jurusan' => 'Teknik Informatika', 'upvote' => 61,  'status' => 'dilaporkan',
         'alasan_laporan' => 'Isi catatan diduga disalin dari buku tanpa mencantumkan sumber.',
         'isi' => "Integral lipat dua dipakai untuk menghitung volume di bawah permukaan z = f(x, y) pada suatu daerah D.\n\nLangkahnya: tentukan batas x dan y, integralkan terhadap satu variabel dulu, lalu variabel lainnya."],
        ['id' => 4, 'judul' => 'Ringkasan Jaringan Komputer — OSI Layer', 'penulis' => 'Dewi Anjani',     'jurusan' => 'Informatika',       'upvote' => 45,  'status' => 'terbit',
         'alasan_laporan' => '',
         'isi' => "Model OSI punya 7 layer: Physical, Data Link, Network, Transport, Session, Presentation, dan Application.\n\nTiap layer punya tugas sendiri, misalnya Network mengurus pengalamatan IP dan routing."],
    ];
    $_SESSION['admin_pengguna'] = [
        ['id' => 1, 'nama' => 'Sarah Faradila',  'nim' => '2551506...003', 'jurusan' => 'Informatika',       'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Nazma Fairuz M.', 'nim' => '2551506...001', 'jurusan' => 'Sistem Informasi',   'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Bagas Wicaksono', 'nim' => '2551506...014', 'jurusan' => 'Teknik Informatika', 'status' => 'diblokir'],
        ['id' => 4, 'nama' => 'Dewi Anjani',     'nim' => '2551506...022', 'jurusan' => 'Informatika',       'status' => 'aktif'],
    ];
    $_SESSION['admin_next_id'] = 5;
    $_SESSION['admin_versi']   = 2;
}

// Token CSRF sederhana untuk semua form
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// ==========================================================
// PROSES AKSI CRUD (POST)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qs    = $_SERVER['QUERY_STRING'] ?? '';
    $balik = basename($_SERVER['PHP_SELF']) . ($qs !== '' ? '?' . $qs : '');

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        flash('error', 'Sesi form tidak valid. Coba ulangi lagi ya.');
        header('Location: ' . $balik);
        exit;
    }

    $entitas = $_POST['entitas'] ?? '';
    $aksi    = $_POST['aksi'] ?? '';
    $id      = (int)($_POST['id'] ?? 0);

    // ---------------- CATATAN ----------------
    if ($entitas === 'catatan') {

        if ($aksi === 'tambah' || $aksi === 'edit') {
            $judul   = trim($_POST['judul'] ?? '');
            $penulis = trim($_POST['penulis'] ?? '');
            $jurusan = trim($_POST['jurusan'] ?? '');
            $upvote  = filter_var($_POST['upvote'] ?? 0, FILTER_VALIDATE_INT);
            $status  = $_POST['status'] ?? 'terbit';
            $isi     = trim($_POST['isi'] ?? '');
            $alasan  = trim($_POST['alasan_laporan'] ?? '');

            $error = null;
            if (panjang($judul) < 3 || panjang($judul) > 150) {
                $error = 'Judul catatan harus 3–150 karakter.';
            } elseif (panjang($penulis) < 2 || panjang($penulis) > 80) {
                $error = 'Nama penulis harus 2–80 karakter.';
            } elseif (panjang($jurusan) < 2 || panjang($jurusan) > 60) {
                $error = 'Jurusan harus 2–60 karakter.';
            } elseif ($upvote === false || $upvote < 0) {
                $error = 'Upvote harus berupa angka 0 atau lebih.';
            } elseif (!in_array($status, ['terbit', 'dilaporkan'], true)) {
                $error = 'Status catatan tidak valid.';
            } elseif (panjang($isi) < 10 || panjang($isi) > 5000) {
                $error = 'Isi catatan harus 10–5000 karakter.';
            } elseif (panjang($alasan) > 200) {
                $error = 'Alasan laporan maksimal 200 karakter.';
            }

            // Alasan laporan hanya berlaku kalau statusnya dilaporkan
            if ($status !== 'dilaporkan') {
                $alasan = '';
            }

            if ($error) {
                flash('error', $error);
            } elseif ($aksi === 'tambah') {
                $_SESSION['admin_catatan'][] = [
                    'id'      => $_SESSION['admin_next_id']++,
                    'judul'   => $judul,
                    'penulis' => $penulis,
                    'jurusan' => $jurusan,
                    'upvote'  => $upvote,
                    'status'  => $status,
                    'isi'     => $isi,
                    'alasan_laporan' => $alasan,
                ];
                flash('sukses', 'Catatan berhasil ditambahkan.');
            } else {
                $ketemu = false;
                foreach ($_SESSION['admin_catatan'] as &$c) {
                    if ($c['id'] === $id) {
                        $c['judul']   = $judul;
                        $c['penulis'] = $penulis;
                        $c['jurusan'] = $jurusan;
                        $c['upvote']  = $upvote;
                        $c['status']  = $status;
                        $c['isi']     = $isi;
                        $c['alasan_laporan'] = $alasan;
                        $ketemu = true;
                        break;
                    }
                }
                unset($c);
                $ketemu ? flash('sukses', 'Catatan berhasil diperbarui.') : flash('error', 'Catatan tidak ditemukan.');
            }

        } elseif ($aksi === 'terbitkan') {
            $ketemu = false;
            foreach ($_SESSION['admin_catatan'] as &$c) {
                if ($c['id'] === $id) {
                    $c['status'] = 'terbit';
                    $c['alasan_laporan'] = '';
                    $ketemu = true;
                    break;
                }
            }
            unset($c);
            $ketemu ? flash('sukses', 'Catatan dinyatakan aman dan diterbitkan kembali.') : flash('error', 'Catatan tidak ditemukan.');

        } elseif ($aksi === 'hapus') {
            $sebelum = count($_SESSION['admin_catatan']);
            $_SESSION['admin_catatan'] = array_values(array_filter(
                $_SESSION['admin_catatan'],
                fn($c) => $c['id'] !== $id
            ));
            count($_SESSION['admin_catatan']) < $sebelum
                ? flash('sukses', 'Catatan berhasil dihapus.')
                : flash('error', 'Catatan tidak ditemukan.');
        }

    // ---------------- PENGGUNA ----------------
    } elseif ($entitas === 'pengguna') {

        if ($aksi === 'tambah' || $aksi === 'edit') {
            $nama    = trim($_POST['nama'] ?? '');
            $nim     = trim($_POST['nim'] ?? '');
            $jurusan = trim($_POST['jurusan'] ?? '');

            $error = null;
            if (panjang($nama) < 2 || panjang($nama) > 80) {
                $error = 'Nama harus 2–80 karakter.';
            } elseif (panjang($nim) < 5 || panjang($nim) > 30) {
                $error = 'NIM harus 5–30 karakter.';
            } elseif (panjang($jurusan) < 2 || panjang($jurusan) > 60) {
                $error = 'Jurusan harus 2–60 karakter.';
            } elseif (nimSudahDipakai($nim, $aksi === 'edit' ? $id : 0)) {
                $error = 'NIM sudah dipakai pengguna lain.';
            }

            if ($error) {
                flash('error', $error);
            } elseif ($aksi === 'tambah') {
                $_SESSION['admin_pengguna'][] = [
                    'id'      => $_SESSION['admin_next_id']++,
                    'nama'    => $nama,
                    'nim'     => $nim,
                    'jurusan' => $jurusan,
                    'status'  => 'aktif',
                ];
                flash('sukses', 'Pengguna berhasil ditambahkan.');
            } else {
                $ketemu = false;
                foreach ($_SESSION['admin_pengguna'] as &$p) {
                    if ($p['id'] === $id) {
                        $p['nama']    = $nama;
                        $p['nim']     = $nim;
                        $p['jurusan'] = $jurusan;
                        $ketemu = true;
                        break;
                    }
                }
                unset($p);
                $ketemu ? flash('sukses', 'Data pengguna berhasil diperbarui.') : flash('error', 'Pengguna tidak ditemukan.');
            }

        } elseif ($aksi === 'blokir' || $aksi === 'bukablokir') {
            $ketemu = false;
            foreach ($_SESSION['admin_pengguna'] as &$p) {
                if ($p['id'] === $id) {
                    $p['status'] = ($aksi === 'blokir') ? 'diblokir' : 'aktif';
                    $ketemu = true;
                    break;
                }
            }
            unset($p);
            if ($ketemu) {
                flash('sukses', $aksi === 'blokir' ? 'Pengguna berhasil diblokir.' : 'Blokir pengguna berhasil dibuka.');
            } else {
                flash('error', 'Pengguna tidak ditemukan.');
            }

        } elseif ($aksi === 'hapus') {
            $sebelum = count($_SESSION['admin_pengguna']);
            $_SESSION['admin_pengguna'] = array_values(array_filter(
                $_SESSION['admin_pengguna'],
                fn($p) => $p['id'] !== $id
            ));
            count($_SESSION['admin_pengguna']) < $sebelum
                ? flash('sukses', 'Pengguna berhasil dihapus.')
                : flash('error', 'Pengguna tidak ditemukan.');
        }
    }

    header('Location: ' . $balik);
    exit;
}

// ==========================================================
// AMBIL DATA UNTUK TAMPILAN (READ)
// ==========================================================
$halaman = $_GET['page'] ?? 'dashboard';
if (!in_array($halaman, ['dashboard', 'catatan', 'pengguna'], true)) {
    $halaman = 'dashboard';
}

$data_catatan  = $_SESSION['admin_catatan'];
$data_pengguna = $_SESSION['admin_pengguna'];

// Statistik dihitung dari data asli
$stats = [
    'total_catatan'  => count($data_catatan),
    'total_pengguna' => count($data_pengguna),
    'total_upvote'   => formatAngka(array_sum(array_column($data_catatan, 'upvote'))),
];

// Catatan yang dilaporkan (untuk panel moderasi di dashboard)
$catatan_dilaporkan = array_values(array_filter($data_catatan, fn($c) => $c['status'] === 'dilaporkan'));

// Jumlah catatan per penulis (dipakai di detail pengguna)
$jumlah_per_penulis = [];
foreach ($data_catatan as $c) {
    $kunci = mb_strtolower($c['penulis'], 'UTF-8');
    $jumlah_per_penulis[$kunci] = ($jumlah_per_penulis[$kunci] ?? 0) + 1;
}

// Pencarian
$kueri_pencarian = trim($_GET['q'] ?? '');

if ($halaman === 'catatan' && $kueri_pencarian !== '') {
    $data_catatan = array_values(array_filter($data_catatan, function ($c) use ($kueri_pencarian) {
        return stripos($c['judul'], $kueri_pencarian) !== false
            || stripos($c['penulis'], $kueri_pencarian) !== false;
    }));
}

if ($halaman === 'pengguna' && $kueri_pencarian !== '') {
    $data_pengguna = array_values(array_filter($data_pengguna, function ($p) use ($kueri_pencarian) {
        return stripos($p['nama'], $kueri_pencarian) !== false
            || stripos($p['nim'], $kueri_pencarian) !== false;
    }));
}

$judul_halaman = [
    'dashboard' => 'Ringkasan sistem',
    'catatan'   => 'Kelola Catatan',
    'pengguna'  => 'Kelola Pengguna',
][$halaman];

$admin_nama    = $_SESSION['admin_nama'] ?? 'Admin';
$admin_inisial = strtoupper(mb_substr($admin_nama, 0, 1, 'UTF-8'));

// Pesan sukses / error (tampil sekali)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Data JSON untuk tombol Lihat / Edit
function dataCatatan($c)
{
    return e(json_encode($c, JSON_UNESCAPED_UNICODE));
}

function dataPengguna($p, $jumlah)
{
    $p['jumlah_catatan'] = $jumlah[mb_strtolower($p['nama'], 'UTF-8')] ?? 0;
    return e(json_encode($p, JSON_UNESCAPED_UNICODE));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($judul_halaman) ?> — Catatin Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="layout">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4z" fill="#4C5FE0"/>
            </svg>
            Admin Panel
        </div>

        <ul class="sidebar-nav">
            <li><a href="?page=dashboard" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="?page=catatan" class="<?= $halaman === 'catatan' ? 'active' : '' ?>">Kelola Catatan</a></li>
            <li><a href="?page=pengguna" class="<?= $halaman === 'pengguna' ? 'active' : '' ?>">Kelola Pengguna</a></li>
            <li><a href="?page=logout">Keluar</a></li>
        </ul>
    </aside>

    <main class="main">
        <div class="main-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Buka menu navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#171A3D" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h2><?= e($judul_halaman) ?></h2>
            </div>
            <div class="avatar"><?= e($admin_inisial) ?></div>
        </div>

        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['tipe']) ?>" id="flashPesan" role="alert">
                <span><?= e($flash['pesan']) ?></span>
                <button type="button" class="flash-tutup" data-tutup-flash aria-label="Tutup pesan">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($halaman === 'dashboard'): ?>

            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['total_catatan']) ?></div>
                    <div class="stat-label">Total catatan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['total_pengguna']) ?></div>
                    <div class="stat-label">Total pengguna</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['total_upvote']) ?></div>
                    <div class="stat-label">Total upvote</div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h3>Catatan dilaporkan (<?= count($catatan_dilaporkan) ?>)</h3>
                    <a href="?page=catatan" class="btn-link btn-lihat" style="margin-left:0;">Kelola catatan &rarr;</a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul catatan</th>
                                <th>Penulis</th>
                                <th>Alasan laporan</th>
                                <th class="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($catatan_dilaporkan)): ?>
                            <tr><td colspan="4" class="kosong">Tidak ada catatan yang dilaporkan. 🎉</td></tr>
                        <?php endif; ?>
                        <?php foreach ($catatan_dilaporkan as $c): ?>
                            <tr>
                                <td><?= e($c['judul']) ?></td>
                                <td><?= e($c['penulis']) ?></td>
                                <td><?= e($c['alasan_laporan'] !== '' ? $c['alasan_laporan'] : '—') ?></td>
                                <td class="aksi">
                                    <div class="dropdown">
                                        <button type="button" class="btn-aksi" data-dropdown aria-haspopup="true" aria-expanded="false">Aksi <span aria-hidden="true">&#9662;</span></button>
                                        <div class="dropdown-menu" role="menu">
                                    <button type="button" class="dropdown-item"
                                            data-lihat data-entitas="catatan"
                                            data-item="<?= dataCatatan($c) ?>">Periksa isi</button>
                                    <button type="button" class="dropdown-item item-sukses"
                                            data-konfirmasi data-entitas="catatan" data-aksi="terbitkan" data-id="<?= $c['id'] ?>"
                                            data-pesan="Nyatakan catatan &quot;<?= e($c['judul']) ?>&quot; aman dan terbitkan kembali?">Terbitkan</button>
                                    <button type="button" class="dropdown-item item-danger"
                                            data-konfirmasi data-entitas="catatan" data-aksi="hapus" data-id="<?= $c['id'] ?>"
                                            data-pesan="Hapus catatan &quot;<?= e($c['judul']) ?>&quot;?">Hapus</button>
                                </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h3>Kelola pengguna</h3>
                    <a href="?page=pengguna" class="btn-link btn-lihat" style="margin-left:0;">Lihat semua &rarr;</a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th class="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($data_pengguna)): ?>
                            <tr><td colspan="4" class="kosong">Belum ada pengguna.</td></tr>
                        <?php endif; ?>
                        <?php foreach (array_slice($data_pengguna, 0, 2) as $p): ?>
                            <tr>
                                <td><?= e($p['nama']) ?></td>
                                <td><?= e($p['nim']) ?></td>
                                <td><?= e($p['jurusan']) ?></td>
                                <td class="aksi">
                                    <div class="dropdown">
                                        <button type="button" class="btn-aksi" data-dropdown aria-haspopup="true" aria-expanded="false">Aksi <span aria-hidden="true">&#9662;</span></button>
                                        <div class="dropdown-menu" role="menu">
                                    <button type="button" class="dropdown-item"
                                            data-lihat data-entitas="pengguna"
                                            data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>">Lihat</button>
                                    <?php if ($p['status'] === 'aktif'): ?>
                                        <button type="button" class="dropdown-item item-danger"
                                                data-konfirmasi data-entitas="pengguna" data-aksi="blokir" data-id="<?= $p['id'] ?>"
                                                data-pesan="Blokir pengguna <?= e($p['nama']) ?>?">Blokir</button>
                                    <?php else: ?>
                                        <button type="button" class="dropdown-item"
                                                data-konfirmasi data-entitas="pengguna" data-aksi="bukablokir" data-id="<?= $p['id'] ?>"
                                                data-pesan="Buka blokir pengguna <?= e($p['nama']) ?>?">Buka Blokir</button>
                                    <?php endif; ?>
                                </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($halaman === 'catatan'): ?>

            <section class="panel">
                <div class="panel-head">
                    <h3>Daftar catatan</h3>
                    <div class="panel-tools">
                        <form method="get" style="margin:0;">
                            <input type="hidden" name="page" value="catatan">
                            <input type="text" name="q" class="search-box" placeholder="Cari judul atau penulis..." value="<?= e($kueri_pencarian) ?>">
                        </form>
                        <button type="button" class="btn-utama" data-tambah data-entitas="catatan">+ Tambah catatan</button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul catatan</th>
                                <th>Penulis</th>
                                <th>Jurusan</th>
                                <th class="tengah">Upvote</th>
                                <th class="tengah">Status</th>
                                <th class="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($data_catatan)): ?>
                            <tr><td colspan="6" class="kosong">Tidak ada catatan yang cocok.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data_catatan as $c): ?>
                                <tr>
                                    <td><?= e($c['judul']) ?></td>
                                    <td><?= e($c['penulis']) ?></td>
                                    <td><?= e($c['jurusan']) ?></td>
                                    <td class="tengah"><?= e($c['upvote']) ?></td>
                                    <td class="tengah">
                                        <?php if ($c['status'] === 'terbit'): ?>
                                            <span class="badge badge-terbit">Terbit</span>
                                        <?php else: ?>
                                            <span class="badge badge-dilaporkan">Dilaporkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="aksi">
                                    <div class="dropdown">
                                        <button type="button" class="btn-aksi" data-dropdown aria-haspopup="true" aria-expanded="false">Aksi <span aria-hidden="true">&#9662;</span></button>
                                        <div class="dropdown-menu" role="menu">
                                        <button type="button" class="dropdown-item"
                                                data-lihat data-entitas="catatan"
                                                data-item="<?= dataCatatan($c) ?>">Lihat</button>
                                        <button type="button" class="dropdown-item"
                                                data-edit data-entitas="catatan"
                                                data-item="<?= dataCatatan($c) ?>">Edit</button>
                                        <?php if ($c['status'] === 'dilaporkan'): ?>
                                            <button type="button" class="dropdown-item item-sukses"
                                                    data-konfirmasi data-entitas="catatan" data-aksi="terbitkan" data-id="<?= $c['id'] ?>"
                                                    data-pesan="Nyatakan catatan &quot;<?= e($c['judul']) ?>&quot; aman dan terbitkan kembali?">Terbitkan</button>
                                        <?php endif; ?>
                                        <button type="button" class="dropdown-item item-danger"
                                                data-konfirmasi data-entitas="catatan" data-aksi="hapus" data-id="<?= $c['id'] ?>"
                                                data-pesan="Hapus catatan &quot;<?= e($c['judul']) ?>&quot;?">Hapus</button>
                                    </div>
                                    </div>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($halaman === 'pengguna'): ?>

            <section class="panel">
                <div class="panel-head">
                    <h3>Daftar pengguna</h3>
                    <div class="panel-tools">
                        <form method="get" style="margin:0;">
                            <input type="hidden" name="page" value="pengguna">
                            <input type="text" name="q" class="search-box" placeholder="Cari nama atau NIM..." value="<?= e($kueri_pencarian) ?>">
                        </form>
                        <button type="button" class="btn-utama" data-tambah data-entitas="pengguna">+ Tambah pengguna</button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th class="tengah">Status</th>
                                <th class="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($data_pengguna)): ?>
                            <tr><td colspan="5" class="kosong">Tidak ada pengguna yang cocok.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data_pengguna as $p): ?>
                                <tr>
                                    <td><?= e($p['nama']) ?></td>
                                    <td><?= e($p['nim']) ?></td>
                                    <td><?= e($p['jurusan']) ?></td>
                                    <td class="tengah">
                                        <?php if ($p['status'] === 'aktif'): ?>
                                            <span class="badge badge-terbit">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-dilaporkan">Diblokir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="aksi">
                                    <div class="dropdown">
                                        <button type="button" class="btn-aksi" data-dropdown aria-haspopup="true" aria-expanded="false">Aksi <span aria-hidden="true">&#9662;</span></button>
                                        <div class="dropdown-menu" role="menu">
                                        <button type="button" class="dropdown-item"
                                                data-lihat data-entitas="pengguna"
                                                data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>">Lihat</button>
                                        <button type="button" class="dropdown-item"
                                                data-edit data-entitas="pengguna"
                                                data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>">Edit</button>
                                        <?php if ($p['status'] === 'aktif'): ?>
                                            <button type="button" class="dropdown-item item-danger"
                                                    data-konfirmasi data-entitas="pengguna" data-aksi="blokir" data-id="<?= $p['id'] ?>"
                                                    data-pesan="Blokir pengguna <?= e($p['nama']) ?>?">Blokir</button>
                                        <?php else: ?>
                                            <button type="button" class="dropdown-item"
                                                    data-konfirmasi data-entitas="pengguna" data-aksi="bukablokir" data-id="<?= $p['id'] ?>"
                                                    data-pesan="Buka blokir pengguna <?= e($p['nama']) ?>?">Buka Blokir</button>
                                        <?php endif; ?>
                                        <button type="button" class="dropdown-item item-danger"
                                                data-konfirmasi data-entitas="pengguna" data-aksi="hapus" data-id="<?= $p['id'] ?>"
                                                data-pesan="Hapus pengguna <?= e($p['nama']) ?>? Tindakan ini tidak bisa dibatalkan.">Hapus</button>
                                    </div>
                                    </div>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php endif; ?>

    </main>
</div>

<!-- Form tersembunyi untuk aksi cepat (hapus, blokir, buka blokir) -->
<form method="post" id="formAksi" hidden>
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="entitas" value="">
    <input type="hidden" name="aksi" value="">
    <input type="hidden" name="id" value="">
</form>

<!-- Modal detail (Lihat) -->
<div class="modal-latar" id="modalLihat" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="judulModalLihat">
        <h3 id="judulModalLihat">Detail</h3>
        <dl class="detail-list" id="isiDetail"></dl>
        <div class="modal-tombol">
            <button type="button" class="btn-batal" data-tutup>Tutup</button>
        </div>
    </div>
</div>

<!-- Modal tambah / edit catatan -->
<div class="modal-latar" id="modalCatatan" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="judulModalCatatan">
        <h3 id="judulModalCatatan">Tambah catatan</h3>
        <form method="post" class="form-modal">
            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
            <input type="hidden" name="entitas" value="catatan">
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="id" value="">

            <label for="catJudul">Judul catatan</label>
            <input type="text" id="catJudul" name="judul" required minlength="3" maxlength="150" placeholder="Contoh: Ringkasan Struktur Data">

            <label for="catPenulis">Penulis</label>
            <input type="text" id="catPenulis" name="penulis" required minlength="2" maxlength="80">

            <label for="catJurusan">Jurusan</label>
            <input type="text" id="catJurusan" name="jurusan" required minlength="2" maxlength="60" list="daftarJurusan">

            <label for="catIsi">Isi catatan</label>
            <textarea id="catIsi" name="isi" required minlength="10" maxlength="5000" rows="6" placeholder="Tulis isi catatan di sini..."></textarea>

            <div class="form-baris">
                <div>
                    <label for="catUpvote">Upvote</label>
                    <input type="number" id="catUpvote" name="upvote" min="0" value="0" required>
                </div>
                <div>
                    <label for="catStatus">Status</label>
                    <select id="catStatus" name="status">
                        <option value="terbit">Terbit</option>
                        <option value="dilaporkan">Dilaporkan</option>
                    </select>
                </div>
            </div>

            <label for="catAlasan">Alasan laporan <span class="opsional">(opsional, hanya jika Dilaporkan)</span></label>
            <input type="text" id="catAlasan" name="alasan_laporan" maxlength="200" placeholder="Contoh: Diduga menyalin tanpa sumber">

            <div class="modal-tombol">
                <button type="button" class="btn-batal" data-tutup>Batal</button>
                <button type="submit" class="btn-utama">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal tambah / edit pengguna -->
<div class="modal-latar" id="modalPengguna" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="judulModalPengguna">
        <h3 id="judulModalPengguna">Tambah pengguna</h3>
        <form method="post" class="form-modal">
            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
            <input type="hidden" name="entitas" value="pengguna">
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="id" value="">

            <label for="penNama">Nama</label>
            <input type="text" id="penNama" name="nama" required minlength="2" maxlength="80">

            <label for="penNim">NIM</label>
            <input type="text" id="penNim" name="nim" required minlength="5" maxlength="30">

            <label for="penJurusan">Jurusan</label>
            <input type="text" id="penJurusan" name="jurusan" required minlength="2" maxlength="60" list="daftarJurusan">

            <div class="modal-tombol">
                <button type="button" class="btn-batal" data-tutup>Batal</button>
                <button type="submit" class="btn-utama">Simpan</button>
            </div>
        </form>
    </div>
</div>

<datalist id="daftarJurusan">
    <option value="Informatika">
    <option value="Sistem Informasi">
    <option value="Teknik Informatika">
</datalist>

<script src="admin.js"></script>
</body>
</html>