<?php
session_start();

$menu = [
    'Dashboard'       => 'admin-dashboard.php',
    'Kelola Catatan'  => 'kelola-catatan.php',
    'Kelola Pengguna' => 'kelola-pengguna.php',
    'Master Data'     => 'master-data.php',
    'Kelola Laporan'  => 'kelola-laporan.php',
    'Keluar'          => 'logout.php',
];
$menuAktif  = 'Kelola Laporan';
$statusList = ['Menunggu', 'Diproses', 'Selesai'];
$filterList = ['Semua', 'Menunggu', 'Diproses', 'Selesai'];

// Catatan: tambahkan pengecekan role admin di sini pada aplikasi sungguhan.

if (!isset($_SESSION['laporan_admin'])) {
    $_SESSION['laporan_admin'] = [
        1 => ['catatan' => 'Soal UAS Basis Data 2025', 'pelapor' => 'Sarah F.', 'alasan' => 'File Rusak',        'status' => 'Diproses', 'keterangan' => 'Tautan file tidak bisa dibuka.'],
        2 => ['catatan' => 'Rangkuman Fisika Dasar',   'pelapor' => 'Naswa S.', 'alasan' => 'Salah Matkul',      'status' => 'Menunggu', 'keterangan' => 'Isinya materi Kimia, bukan Fisika.'],
        3 => ['catatan' => 'Modul Jaringan Komputer',  'pelapor' => 'Nazma F.', 'alasan' => 'Spam',              'status' => 'Selesai',  'keterangan' => 'Berisi iklan di setiap halaman.'],
    ];
}

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(16));
}

function e(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}

// Permintaan AJAX: ubah status / hapus laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $ok = false;
    $id = (int) ($_POST['id'] ?? 0);

    if (hash_equals($_SESSION['token'], $_POST['token'] ?? '') && isset($_SESSION['laporan_admin'][$id])) {
        $aksi = $_POST['aksi'] ?? '';

        if ($aksi === 'status' && in_array($_POST['status'] ?? '', $statusList, true)) {
            $_SESSION['laporan_admin'][$id]['status'] = $_POST['status'];
            $ok = true;
        } elseif ($aksi === 'hapus' && $_SESSION['laporan_admin'][$id]['status'] === 'Selesai') {
            unset($_SESSION['laporan_admin'][$id]);
            $ok = true;
        }
    }

    echo json_encode(['ok' => $ok]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="token" content="<?= e($_SESSION['token']) ?>">
    <title>Laporan Report</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="report_admin.css">
</head>

<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <header class="logo">
            <svg width="13" height="15" viewBox="0 0 13 15" aria-hidden="true">
                <path d="M6.5 0 0 2.2v5c0 3.6 2.6 6.2 6.5 7.8C10.4 13.4 13 10.8 13 7.2v-5L6.5 0z" fill="#4a90e2" />
            </svg>
            <strong>Admin Panel</strong>
        </header>
        <nav class="sidebar-nav">

            <a href="../dashboard_admin/dashboard.php" class="nav-item">
                Dashboard
            </a>

            <a href="../kelola_catatan/catatan.php" class="nav-item">
                Kelola Catatan
            </a>

            <a href="../kelola_pengguna/pengguna.php" class="nav-item">
                Kelola Pengguna
            </a>

            <a href="../master_data/index.php" class="nav-item">
                Master Data
            </a>

            <a href="../report/report_admin.php" class="nav-item active">
                Kelola Laporan
            </a>

            <a href="../logout.php" class="nav-item btn-logout"
                onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                Keluar
            </a>

        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="konten">
        <header class="topbar">
            <h1>Laporan masuk</h1>
            <span class="avatar">A</span>
        </header>

        <!-- FILTER -->
        <nav class="filter" aria-label="Filter status">
            <?php foreach ($filterList as $i => $f): ?>
                <button type="button" class="chip<?= $i === 0 ? ' aktif' : '' ?>" data-filter="<?= e($f) ?>"><?= e($f) ?></button>
            <?php endforeach; ?>
        </nav>

        <!-- TABEL LAPORAN -->
        <table>
            <thead>
                <tr>
                    <th>Catatan</th>
                    <th>Pelapor</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="daftarLaporan">
                <?php foreach ($_SESSION['laporan_admin'] as $id => $lap): ?>
                    <tr data-id="<?= $id ?>" data-status="<?= e($lap['status']) ?>" data-keterangan="<?= e($lap['keterangan']) ?>">
                        <td><?= e($lap['catatan']) ?></td>
                        <td><?= e($lap['pelapor']) ?></td>
                        <td><?= e($lap['alasan']) ?></td>
                        <td>
                            <span class="status <?= strtolower($lap['status']) ?>">
                                <select class="pilih-status" aria-label="Ubah status">
                                    <?php foreach ($statusList as $s): ?>
                                        <option value="<?= e($s) ?>" <?= $s === $lap['status'] ? ' selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </td>
                        <td class="aksi">
                            <a href="#" class="detail">Detail</a>
                            <a href="#" class="hapus">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="kosong" hidden>
                    <td colspan="5">Tidak ada laporan.</td>
                </tr>
            </tbody>
        </table>
    </main>

    <!-- DETAIL LAPORAN -->
    <dialog id="dialogDetail">
        <h2>Detail laporan</h2>
        <dl>
            <dt>Catatan</dt>
            <dd id="dCatatan"></dd>
            <dt>Pelapor</dt>
            <dd id="dPelapor"></dd>
            <dt>Alasan</dt>
            <dd id="dAlasan"></dd>
            <dt>Status</dt>
            <dd id="dStatus"></dd>
            <dt>Keterangan</dt>
            <dd id="dKeterangan"></dd>
        </dl>
        <form method="dialog">
            <button class="tutup">Tutup</button>
        </form>
    </dialog>

    <!-- JAVASCRIPT -->
    <script src="report_admin.js"></script>
</body>

</html>