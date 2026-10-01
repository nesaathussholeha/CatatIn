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

$menu = [
    'Dashboard'      => '#',
    'Unggah Catatan' => '#',
    'Catatan Saya'   => '#',
    'Koleksi Belajar'=> '#',
    'Tugas Belajar'  => 'tugas.php',
    'Profil'         => '#',
    'Keluar'         => '#',
];
$menuAktif = 'Tugas Belajar';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tugas Belajar - CatatIn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>

<div class="layout">

    <!-- ===== Sidebar ===== -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-kepala">
            <div class="logo">📖 CatatIn</div>
            <button type="button" class="sidebar-tutup" id="sidebarTutup" aria-label="Tutup menu navigasi">&times;</button>
        </div>
        <nav>
            <?php foreach ($menu as $nama => $link): ?>
                <a href="<?= e($link) ?>" class="menu-item <?= $nama === $menuAktif ? 'aktif' : '' ?>">
                    <?= e($nama) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- ===== Konten utama ===== -->
    <main class="konten">
        <header class="konten-header">
            <div class="header-kiri">
                <button type="button" class="menu-toggle" id="menuToggle"
                        aria-label="Buka menu navigasi" aria-controls="sidebar" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#171A3D" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1>Tugas belajar saya</h1>
            </div>
            <button type="button" class="btn-utama" id="btnTambah" aria-label="Tambah tugas"><span class="plus">+</span><span class="teks-tambah"> Tambah tugas</span></button>
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
    </main>
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