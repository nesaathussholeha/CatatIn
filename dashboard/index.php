<?php
session_start();

// Logout
if (($_GET['page'] ?? '') === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: ../login.php');
    exit;
}

// Helper
function e($nilai) { return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8'); }
function flash($tipe, $pesan) { $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan]; }
function formatAngka($n) { return ($n >= 1000) ? rtrim(rtrim(number_format($n / 1000, 1, '.', ''), '0'), '.') . 'k' : (string)$n; }

// Dummy Data Init
if (!isset($_SESSION['admin_catatan']) || ($_SESSION['admin_versi'] ?? 0) < 2) {
    $_SESSION['admin_catatan'] = [
        ['id' => 1, 'judul' => 'Ringkasan Struktur Data — Binary Tree',  'penulis' => 'Sarah Faradila',  'jurusan' => 'Informatika',       'upvote' => 128, 'status' => 'terbit', 'alasan_laporan' => '', 'isi' => "Binary tree adalah struktur data berbentuk pohon..."],
        ['id' => 2, 'judul' => 'Rangkuman Basis Data Relasional',         'penulis' => 'Nazma Fairuz M.', 'jurusan' => 'Sistem Informasi',   'upvote' => 94,  'status' => 'terbit', 'alasan_laporan' => '', 'isi' => "Basis data relasional menyimpan data dalam tabel..."],
        ['id' => 3, 'judul' => 'Catatan Kalkulus II — Integral Lipat',    'penulis' => 'Bagas Wicaksono', 'jurusan' => 'Teknik Informatika', 'upvote' => 61,  'status' => 'dilaporkan', 'alasan_laporan' => 'Isi catatan diduga disalin tanpa sumber.', 'isi' => "Integral lipat dua dipakai untuk menghitung volume..."],
        ['id' => 4, 'judul' => 'Ringkasan Jaringan Komputer — OSI Layer', 'penulis' => 'Dewi Anjani',     'jurusan' => 'Informatika',       'upvote' => 45,  'status' => 'terbit', 'alasan_laporan' => '', 'isi' => "Model OSI punya 7 layer..."],
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

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Handler POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        flash('error', 'Sesi form tidak valid.');
        header('Location: index.php');
        exit;
    }

    $entitas = $_POST['entitas'] ?? '';
    $aksi    = $_POST['aksi'] ?? '';
    $id      = (int)($_POST['id'] ?? 0);

    if ($entitas === 'catatan') {
        if ($aksi === 'terbitkan') {
            foreach ($_SESSION['admin_catatan'] as &$c) {
                if ($c['id'] === $id) { $c['status'] = 'terbit'; $c['alasan_laporan'] = ''; break; }
            }
            flash('sukses', 'Catatan diterbitkan kembali.');
        } elseif ($aksi === 'hapus') {
            $_SESSION['admin_catatan'] = array_values(array_filter($_SESSION['admin_catatan'], fn($c) => $c['id'] !== $id));
            flash('sukses', 'Catatan berhasil dihapus.');
        }
    } elseif ($entitas === 'pengguna' && ($aksi === 'blokir' || $aksi === 'bukablokir')) {
        foreach ($_SESSION['admin_pengguna'] as &$p) {
            if ($p['id'] === $id) { $p['status'] = ($aksi === 'blokir') ? 'diblokir' : 'aktif'; break; }
        }
        flash('sukses', 'Status pengguna diperbarui.');
    }

    header('Location: index.php');
    exit;
}

$data_catatan  = $_SESSION['admin_catatan'];
$data_pengguna = $_SESSION['admin_pengguna'];
$stats = [
    'total_catatan'  => count($data_catatan),
    'total_pengguna' => count($data_pengguna),
    'total_upvote'   => formatAngka(array_sum(array_column($data_catatan, 'upvote'))),
];
$catatan_dilaporkan = array_values(array_filter($data_catatan, fn($c) => $c['status'] === 'dilaporkan'));

$jumlah_per_penulis = [];
foreach ($data_catatan as $c) {
    $kunci = mb_strtolower($c['penulis'], 'UTF-8');
    $jumlah_per_penulis[$kunci] = ($jumlah_per_penulis[$kunci] ?? 0) + 1;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function dataCatatan($c) { return e(json_encode($c, JSON_UNESCAPED_UNICODE)); }
function dataPengguna($p, $jumlah) {
    $p['jumlah_catatan'] = $jumlah[mb_strtolower($p['nama'], 'UTF-8')] ?? 0;
    return e(json_encode($p, JSON_UNESCAPED_UNICODE));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Sistem — Catatin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4z" fill="#4C5FE0"/></svg>
            Admin Panel
        </div>
        <ul class="sidebar-nav">
            <li><a href="../dashboard/" class="active">Dashboard</a></li>
            <li><a href="../catatan/">Kelola Catatan</a></li>
            <li><a href="../pengguna/">Kelola Pengguna</a></li>
            <li><a href="index.php?page=logout">Keluar</a></li>
        </ul>
    </aside>

    <main class="main">
        <div class="main-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Buka menu navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#171A3D" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h2>Ringkasan sistem</h2>
            </div>
            <div class="avatar">A</div>
        </div>

        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['tipe']) ?>" id="flashPesan" role="alert">
                <span><?= e($flash['pesan']) ?></span>
                <button type="button" class="flash-tutup" data-tutup-flash aria-label="Tutup pesan">&times;</button>
            </div>
        <?php endif; ?>

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
                <a href="../catatan/" class="btn-link btn-lihat" style="margin-left:0;">Kelola catatan &rarr;</a>
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
                                        <button type="button" class="dropdown-item" data-lihat data-entitas="catatan" data-item="<?= dataCatatan($c) ?>">Periksa isi</button>
                                        <button type="button" class="dropdown-item item-sukses" data-konfirmasi data-entitas="catatan" data-aksi="terbitkan" data-id="<?= $c['id'] ?>" data-pesan="Nyatakan catatan &quot;<?= e($c['judul']) ?>&quot; aman dan terbitkan kembali?">Terbitkan</button>
                                        <button type="button" class="dropdown-item item-danger" data-konfirmasi data-entitas="catatan" data-aksi="hapus" data-id="<?= $c['id'] ?>" data-pesan="Hapus catatan &quot;<?= e($c['judul']) ?>&quot;?">Hapus</button>
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
                <a href="../pengguna/" class="btn-link btn-lihat" style="margin-left:0;">Lihat semua &rarr;</a>
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
                                        <button type="button" class="dropdown-item" data-lihat data-entitas="pengguna" data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>">Lihat</button>
                                        <?php if ($p['status'] === 'aktif'): ?>
                                            <button type="button" class="dropdown-item item-danger" data-konfirmasi data-entitas="pengguna" data-aksi="blokir" data-id="<?= $p['id'] ?>" data-pesan="Blokir pengguna <?= e($p['nama']) ?>?">Blokir</button>
                                        <?php else: ?>
                                            <button type="button" class="dropdown-item" data-konfirmasi data-entitas="pengguna" data-aksi="bukablokir" data-id="<?= $p['id'] ?>" data-pesan="Buka blokir pengguna <?= e($p['nama']) ?>?">Buka Blokir</button>
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
    </main>
</div>

<form method="post" id="formAksi" hidden>
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="entitas" value="">
    <input type="hidden" name="aksi" value="">
    <input type="hidden" name="id" value="">
</form>

<div class="modal-latar" id="modalLihat" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="judulModalLihat">
        <h3 id="judulModalLihat">Detail</h3>
        <dl class="detail-list" id="isiDetail"></dl>
        <div class="modal-tombol">
            <button type="button" class="btn-batal" data-tutup>Tutup</button>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>