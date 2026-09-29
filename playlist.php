<?php
/**
 * koleksi-tanpa-db.php — Modul Koleksi / Folder Belajar (versi TANPA database)
 * Untuk presentasi progres tengah semester: belum ada koneksi DB / logika bisnis,
 * data cuma disimpan sementara di session (hilang kalau browser/session direset).
 *
 * Alur URL sama seperti versi database:
 *   koleksi-tanpa-db.php        -> daftar semua koleksi
 *   koleksi-tanpa-db.php?id=1   -> detail satu koleksi
 *
 * Begitu modul DB & auth tim sudah siap, tinggal pindah ke koleksi-satu-file.php
 * (versi PDO/MySQL) yang sudah dibuat sebelumnya — struktur HTML/CSS-nya sama persis.
 */

session_start();

// Nama file ini sendiri, dibaca otomatis dari URL -> aman walau file di-rename
// (misal jadi playlist.php), semua link & redirect di bawah ikut menyesuaikan.
$namaFile = basename($_SERVER['PHP_SELF']);

// ------------------------------------------------------------------
// "Katalog catatan" contoh, sekadar biar ada isi buat didemoin.
// Nanti ini diganti data asli dari modul katalog/upload catatan tim.
// ------------------------------------------------------------------
$catatanKatalog = [
    1 => ['id' => 1, 'judul' => 'Rangkuman Struktur Data — Pohon & Graf', 'jurusan' => 'Informatika', 'tipe' => 'Rangkuman', 'penyusun' => 'Nazma F.'],
    2 => ['id' => 2, 'judul' => 'Kumpulan Soal UAS Basis Data 2025', 'jurusan' => 'Sistem Informasi', 'tipe' => 'Soal Ujian', 'penyusun' => 'Anonim'],
    3 => ['id' => 3, 'judul' => 'Modul Praktikum Rangkaian Digital', 'jurusan' => 'Elektro', 'tipe' => 'Modul', 'penyusun' => 'Naswa S.'],
    4 => ['id' => 4, 'judul' => 'Rangkuman Pemrograman Web — Sesi 1-7', 'jurusan' => 'Informatika', 'tipe' => 'Rangkuman', 'penyusun' => 'Afifatul M.'],
];

// ------------------------------------------------------------------
// "Basis data" sementara di session
// $_SESSION['koleksi'][id] = ['id'=>, 'nama'=>, 'deskripsi'=>, 'catatan_ids'=>[urut...]]
// ------------------------------------------------------------------
if (!isset($_SESSION['koleksi'])) {
    $_SESSION['koleksi'] = [];
}
if (!isset($_SESSION['koleksi_next_id'])) {
    $_SESSION['koleksi_next_id'] = 1;
}

function ambilFlash()
{
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}
function alihkanDenganPesan(string $lokasi, string $tipe, string $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
    header('Location: ' . $lokasi);
    exit;
}

$koleksiId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$aksi      = $_POST['aksi'] ?? '';

// ------------------------------------------------------------------
// CREATE — buat koleksi baru
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'buat') {
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        alihkanDenganPesan($namaFile, 'gagal', 'Nama koleksi wajib diisi.');
    }

    $id = $_SESSION['koleksi_next_id']++;
    $_SESSION['koleksi'][$id] = [
        'id'          => $id,
        'nama'        => $nama,
        'deskripsi'   => $deskripsi,
        'catatan_ids' => [],
    ];

    alihkanDenganPesan($namaFile, 'sukses', 'Koleksi "' . $nama . '" berhasil dibuat.');
}

// ------------------------------------------------------------------
// DELETE — hapus koleksi (catatan di katalog tidak ikut terhapus, cuma
// referensinya di koleksi ini yang dibuang)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'hapus') {
    $id = (int) ($_POST['id'] ?? 0);
    unset($_SESSION['koleksi'][$id]);
    alihkanDenganPesan($namaFile, 'sukses', 'Koleksi berhasil dihapus.');
}

// ------------------------------------------------------------------
// UPDATE — ganti nama & deskripsi
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'perbarui' && $koleksiId && isset($_SESSION['koleksi'][$koleksiId])) {
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        alihkanDenganPesan($namaFile . '?id=' . $koleksiId, 'gagal', 'Nama koleksi wajib diisi.');
    }

    $_SESSION['koleksi'][$koleksiId]['nama']      = $nama;
    $_SESSION['koleksi'][$koleksiId]['deskripsi'] = $deskripsi;

    alihkanDenganPesan($namaFile . '?id=' . $koleksiId, 'sukses', 'Koleksi berhasil diperbarui.');
}

// ------------------------------------------------------------------
// UPDATE — tambah catatan ke koleksi
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'tambah_catatan' && $koleksiId && isset($_SESSION['koleksi'][$koleksiId])) {
    $catatanId = (int) ($_POST['catatan_id'] ?? 0);
    if ($catatanId > 0 && !in_array($catatanId, $_SESSION['koleksi'][$koleksiId]['catatan_ids'], true)) {
        $_SESSION['koleksi'][$koleksiId]['catatan_ids'][] = $catatanId;
    }
    header('Location: ' . $namaFile . '?id=' . $koleksiId);
    exit;
}

// ------------------------------------------------------------------
// UPDATE — keluarkan catatan dari koleksi
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'keluarkan_catatan' && $koleksiId && isset($_SESSION['koleksi'][$koleksiId])) {
    $catatanId = (int) ($_POST['catatan_id'] ?? 0);
    $_SESSION['koleksi'][$koleksiId]['catatan_ids'] = array_values(array_filter(
        $_SESSION['koleksi'][$koleksiId]['catatan_ids'],
        fn($cid) => $cid !== $catatanId
    ));
    header('Location: ' . $namaFile . '?id=' . $koleksiId);
    exit;
}

// ------------------------------------------------------------------
// UPDATE — susun ulang urutan catatan (naik/turun)
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aksi === 'geser' && $koleksiId && isset($_SESSION['koleksi'][$koleksiId])) {
    $catatanId = (int) ($_POST['catatan_id'] ?? 0);
    $arah      = $_POST['arah'] ?? '';
    $daftar    = $_SESSION['koleksi'][$koleksiId]['catatan_ids'];

    $indeks = array_search($catatanId, $daftar, true);
    if ($indeks !== false) {
        if ($arah === 'naik' && $indeks > 0) {
            [$daftar[$indeks - 1], $daftar[$indeks]] = [$daftar[$indeks], $daftar[$indeks - 1]];
        } elseif ($arah === 'turun' && $indeks < count($daftar) - 1) {
            [$daftar[$indeks + 1], $daftar[$indeks]] = [$daftar[$indeks], $daftar[$indeks + 1]];
        }
    }
    $_SESSION['koleksi'][$koleksiId]['catatan_ids'] = $daftar;

    header('Location: ' . $namaFile . '?id=' . $koleksiId);
    exit;
}

// ------------------------------------------------------------------
// Tentukan mode tampilan
// ------------------------------------------------------------------
$koleksi = ($koleksiId && isset($_SESSION['koleksi'][$koleksiId])) ? $_SESSION['koleksi'][$koleksiId] : null;
if ($koleksiId && !$koleksi) {
    alihkanDenganPesan($namaFile, 'gagal', 'Koleksi tidak ditemukan.');
}
$modeDetail = $koleksi !== null;
$modeEdit   = $modeDetail && isset($_GET['edit']);
$flash      = ambilFlash();

if ($modeDetail) {
    $isiKoleksi = [];
    foreach ($koleksi['catatan_ids'] as $cid) {
        if (isset($catatanKatalog[$cid])) $isiKoleksi[] = $catatanKatalog[$cid];
    }
    $jumlahCatatan   = count($isiKoleksi);
    $catatanTersedia = array_filter($catatanKatalog, fn($c) => !in_array($c['id'], $koleksi['catatan_ids'], true));
} else {
    $daftarKoleksi = array_map(function ($k) {
        $k['jumlah_catatan'] = count($k['catatan_ids']);
        return $k;
    }, $_SESSION['koleksi']);
    $daftarKoleksi = array_reverse($daftarKoleksi); // terbaru di atas
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $modeDetail ? htmlspecialchars($koleksi['nama']) . ' — Koleksi Saya' : 'Koleksi Saya — Notes Hub' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --navy:#171A3D; --indigo:#4C5FE0; --indigo-dark:#3B4BC7; --lavender:#EEF1FB;
  --slate:#64748B; --line:#E1E4F2; --green:#16A34A; --amber:#F59E0B; --red:#DC2626;
  --card:#ffffff; --radius-lg:18px; --radius-md:12px;
}
*{box-sizing:border-box;}
html, body{ height:100%; margin:0; font-family:"Inter",-apple-system,BlinkMacSystemFont,sans-serif; font-size:15px; line-height:1.6; background:var(--lavender); color:var(--navy); }
a{color:inherit; text-decoration:none;}
h1,h2,h3,h4,h5,h6{ font-family:"Poppins","Inter",sans-serif; color:var(--navy); margin:0; }
button{ font:inherit; }

.app{ display:flex; min-height:100vh; width:100%; background:var(--card); }

.sidebar{ width:230px; flex-shrink:0; background:var(--navy); color:#c7cae0; padding:28px 18px; display:flex; flex-direction:column; gap:28px; position:sticky; top:0; height:100vh; overflow-y:auto; }
.brand{ display:flex; align-items:center; font-family:"Poppins",sans-serif; font-weight:600; font-size:1.05rem; color:#fff; padding:0 6px; }
nav.menu{ display:flex; flex-direction:column; gap:4px; }
nav.menu a{ padding:11px 14px; border-radius:10px; font-family:"Poppins",sans-serif; font-weight:500; font-size:0.92rem; color:#c7cae0; transition:background .15s ease, color .15s ease; }
nav.menu a:hover{ background:rgba(255,255,255,0.08); color:#fff; }
nav.menu a.active{ background:var(--indigo); color:#fff; }
.sidebar .keluar{ margin-top:auto; }
.sidebar-close{ display:none; }
.overlay{ display:none; position:fixed; inset:0; background:rgba(23,26,61,0.45); z-index:40; }
.menu-toggle{ display:none; align-items:center; gap:8px; border:1px solid rgba(76,95,224,0.3); background:var(--lavender); padding:9px 12px; border-radius:10px; font-family:"Poppins",sans-serif; font-weight:500; font-size:0.85rem; color:var(--indigo); cursor:pointer; flex-shrink:0; transition:background .15s ease; }
.menu-toggle:hover{ background:#E4E8FB; }

@media (max-width:900px){
  .menu-toggle{ display:flex; }
  .sidebar{ position:fixed; top:0; left:0; height:100vh; z-index:50; transform:translateX(-100%); transition:transform .22s ease; }
  body.sidebar-open .sidebar{ transform:translateX(0); box-shadow:20px 0 40px -20px rgba(0,0,0,0.35); }
  body.sidebar-open .overlay{ display:block; }
  .sidebar-close{ display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:rgba(255,255,255,0.1); color:#fff; margin-left:auto; cursor:pointer; border:none; }
}

.main{ flex:1; padding:clamp(16px,4vw,26px) clamp(16px,4vw,34px) 40px; min-width:0; }
.crumb{ font-size:0.8rem; color:var(--slate); margin-bottom:18px; }
.crumb a:hover{ color:var(--indigo); }
.topbar{ display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:22px; }
.topbar .judul-wrap{ flex:1; min-width:200px; display:flex; align-items:center; gap:10px; }
.topbar h1{ font-weight:600; font-size:clamp(1.4rem, 3vw, 1.85rem); line-height:1.25; letter-spacing:-0.01em; }

.btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 18px; border-radius:10px; border:1px solid transparent; font-family:"Poppins",sans-serif; font-weight:500; font-size:0.87rem; cursor:pointer; white-space:nowrap; transition:background .15s ease, border-color .15s ease, color .15s ease; }
.btn-primary{ background:var(--indigo); color:#fff; }
.btn-primary:hover{ background:var(--indigo-dark); }
.btn-secondary{ background:var(--card); color:var(--navy); border-color:var(--line); }
.btn-secondary:hover{ border-color:var(--indigo); color:var(--indigo); }
.btn-text{ background:none; border:none; color:var(--slate); font-family:"Inter",sans-serif; font-size:0.85rem; padding:4px 2px; cursor:pointer; }
.btn-text:hover{ color:var(--navy); }
.btn-text.danger:hover{ color:var(--red); }
.btn-icon{ width:30px; height:30px; border-radius:8px; border:1px solid var(--line); background:var(--card); color:var(--slate); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
.btn-icon:hover{ border-color:var(--indigo); color:var(--indigo); }
.btn-icon:disabled{ opacity:0.35; cursor:not-allowed; }

.alert{ padding:12px 16px; border-radius:12px; font-size:0.87rem; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
.alert-sukses{ background:#E5F6EB; color:#0F7A3D; }
.alert-gagal{ background:#FCE8E8; color:#B42318; }

.koleksi-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:16px; }
.koleksi-card{ border:1px solid var(--line); border-radius:var(--radius-lg); padding:20px; background:var(--card); display:flex; flex-direction:column; gap:10px; }
.koleksi-card .folder-icon{ width:34px; height:26px; border-radius:4px 8px 8px 8px; background:var(--lavender); border:1px solid var(--line); position:relative; }
.koleksi-card .folder-icon::before{ content:""; position:absolute; top:-6px; left:0; width:16px; height:6px; background:var(--lavender); border:1px solid var(--line); border-bottom:none; border-radius:4px 4px 0 0; }
.koleksi-card h3{ font-weight:600; font-size:1.02rem; line-height:1.4; }
.koleksi-card .desk{ color:var(--slate); font-size:0.85rem; min-height:1.4em; }
.koleksi-card .meta{ color:var(--slate); font-size:0.8rem; }
.koleksi-card .aksi{ margin-top:6px; display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid var(--line); }

.empty{ grid-column:1 / -1; text-align:center; padding:48px 20px; color:var(--slate); border:1px dashed var(--line); border-radius:var(--radius-lg); }

.modal-overlay{ display:none; position:fixed; inset:0; z-index:60; background:rgba(23,26,61,0.45); align-items:center; justify-content:center; padding:20px; }
body.modal-open .modal-overlay{ display:flex; }
.modal-box{ background:var(--card); border-radius:var(--radius-lg); padding:24px; width:100%; max-width:420px; display:flex; flex-direction:column; gap:14px; }
.modal-box h2{ font-size:1.15rem; font-weight:600; }
.field{ display:flex; flex-direction:column; gap:6px; }
.field label{ font-family:"Poppins",sans-serif; font-weight:500; font-size:0.85rem; color:var(--navy); }
.field input[type="text"], .field textarea, .field select{ width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--line); font-family:"Inter",sans-serif; font-size:0.9rem; color:var(--navy); background:var(--card); }
.field input:focus, .field textarea:focus, .field select:focus{ outline:none; border-color:var(--indigo); }
.field textarea{ resize:vertical; min-height:70px; }
.modal-actions{ display:flex; justify-content:flex-end; gap:10px; margin-top:4px; }

.detail-head{ border:1px solid var(--line); border-radius:var(--radius-lg); padding:22px; margin-bottom:24px; display:flex; flex-direction:column; gap:14px; }
.detail-head .info h2{ font-size:1.25rem; font-weight:600; margin-bottom:6px; }
.detail-head .info p{ color:var(--slate); font-size:0.9rem; margin:0; }
.detail-head .edit-form{ display:none; flex-direction:column; gap:12px; }
.detail-head.mode-edit .edit-form{ display:flex; }
.detail-head.mode-edit .info{ display:none; }
.detail-head .head-actions{ display:flex; gap:10px; flex-wrap:wrap; }

.section-title{ font-family:"Poppins",sans-serif; font-weight:600; font-size:1rem; margin:28px 0 12px; }

.note-list{ display:flex; flex-direction:column; gap:10px; }
.note-row{ display:flex; align-items:center; gap:14px; border:1px solid var(--line); border-radius:var(--radius-md); padding:14px 16px; background:var(--card); }
.note-row .reorder{ display:flex; flex-direction:column; gap:2px; }
.note-row .info{ flex:1; min-width:0; }
.note-row .info h4{ font-weight:600; font-size:0.95rem; line-height:1.4; }
.note-row .info .meta{ color:var(--slate); font-size:0.8rem; margin-top:2px; }
.note-row .tag{ display:inline-block; font-family:"Poppins",sans-serif; font-size:0.7rem; font-weight:500; padding:3px 9px; border-radius:999px; background:#EAEDFC; color:var(--indigo); margin-bottom:4px; }

.add-note-row{ display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; border:1px dashed var(--line); border-radius:var(--radius-md); padding:16px; }
.add-note-row .field{ flex:1; min-width:220px; }

@media (max-width:480px){
  .add-note-row{ flex-direction:column; align-items:stretch; }
  .add-note-row .btn{ width:100%; }
}
</style>
</head>
<body>
<div class="overlay" onclick="tutupSidebar()"></div>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="brand">
      Notes Hub
      <button type="button" class="sidebar-close" onclick="tutupSidebar()" aria-label="Tutup menu">✕</button>
    </div>
    <nav class="menu">
      <a href="dashboard_user.php" title="dashboard">Dashboard</a>
      <a href="#" title="Halaman ini dibuat anggota tim lain">Unggah Catatan</a>
      <a href="#" title="Halaman ini dibuat anggota tim lain">Catatan Saya</a>
      <a href="<?= $namaFile ?>" class="active">Koleksi Saya</a>
      <a href="#" title="Halaman ini dibuat anggota tim lain">Profil</a>
    </nav>
    <nav class="menu keluar">
      <a href="#" title="Halaman ini dibuat anggota tim lain">Keluar</a>
    </nav>
  </aside>

  <main class="main">

    <?php if (!$modeDetail): ?>
      <!-- ================= MODE DAFTAR ================= -->
      <div class="crumb">/koleksi</div>

      <div class="topbar">
        <button type="button" class="menu-toggle" onclick="bukaSidebar()" aria-label="Buka menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
          Menu
        </button>
        <div class="judul-wrap"><h1>Koleksi Saya</h1></div>
        <button type="button" class="btn btn-primary" onclick="bukaModal()">+ Buat Koleksi Baru</button>
      </div>

      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['tipe'] === 'sukses' ? 'sukses' : 'gagal' ?>"><?= htmlspecialchars($flash['pesan']) ?></div>
      <?php endif; ?>

      <div class="koleksi-grid">
        <?php if (empty($daftarKoleksi)): ?>
          <div class="empty">Belum ada koleksi. Buat folder belajar pertamamu, misalnya "Bahan UTS Pemrograman Web".</div>
        <?php else: ?>
          <?php foreach ($daftarKoleksi as $k): ?>
            <article class="koleksi-card">
              <span class="folder-icon"></span>
              <h3><?= htmlspecialchars($k['nama']) ?></h3>
              <p class="desk"><?= htmlspecialchars($k['deskripsi'] ?? '') ?></p>
              <p class="meta"><?= (int) $k['jumlah_catatan'] ?> catatan tersimpan</p>
              <div class="aksi">
                <a class="btn btn-secondary" href="<?= $namaFile ?>?id=<?= (int) $k['id'] ?>">Buka</a>
                <form method="post" onsubmit="return confirm('Hapus koleksi ini? Catatan aslinya tetap aman di katalog.');">
                  <input type="hidden" name="aksi" value="hapus">
                  <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
                  <button type="submit" class="btn-text danger">Hapus</button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="modal-overlay" onclick="if(event.target===this) tutupModal()">
        <div class="modal-box">
          <h2>Buat Koleksi Baru</h2>
          <form method="post">
            <input type="hidden" name="aksi" value="buat">
            <div class="field" style="margin-bottom:12px;">
              <label for="nama">Nama koleksi</label>
              <input type="text" id="nama" name="nama" placeholder="Contoh: Bahan UTS Pemrograman Web" required>
            </div>
            <div class="field">
              <label for="deskripsi">Deskripsi (opsional)</label>
              <textarea id="deskripsi" name="deskripsi" placeholder="Catatan singkat tentang isi koleksi ini..."></textarea>
            </div>
            <div class="modal-actions">
              <button type="button" class="btn btn-secondary" onclick="tutupModal()">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
      <?php if ($flash && $flash['tipe'] === 'gagal'): ?><script>document.body.classList.add('modal-open');</script><?php endif; ?>

    <?php else: ?>
      <!-- ================= MODE DETAIL ================= -->
      <div class="crumb"><a href="<?= $namaFile ?>">Koleksi Saya</a> / <?= htmlspecialchars($koleksi['nama']) ?></div>

      <div class="topbar">
        <button type="button" class="menu-toggle" onclick="bukaSidebar()" aria-label="Buka menu">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
          Menu
        </button>
        <div class="judul-wrap"><h1>Koleksi Saya</h1></div>
      </div>

      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['tipe'] === 'sukses' ? 'sukses' : 'gagal' ?>"><?= htmlspecialchars($flash['pesan']) ?></div>
      <?php endif; ?>

      <div class="detail-head <?= $modeEdit ? 'mode-edit' : '' ?>">
        <div class="info">
          <h2><?= htmlspecialchars($koleksi['nama']) ?></h2>
          <p><?= htmlspecialchars($koleksi['deskripsi'] !== '' ? $koleksi['deskripsi'] : 'Belum ada deskripsi.') ?></p>
        </div>

        <form class="edit-form" method="post">
          <input type="hidden" name="aksi" value="perbarui">
          <div class="field">
            <label for="edit-nama">Nama koleksi</label>
            <input type="text" id="edit-nama" name="nama" value="<?= htmlspecialchars($koleksi['nama']) ?>" required>
          </div>
          <div class="field">
            <label for="edit-deskripsi">Deskripsi</label>
            <textarea id="edit-deskripsi" name="deskripsi"><?= htmlspecialchars($koleksi['deskripsi']) ?></textarea>
          </div>
          <div class="modal-actions" style="justify-content:flex-start;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a class="btn btn-secondary" href="<?= $namaFile ?>?id=<?= $koleksiId ?>">Batal</a>
          </div>
        </form>

        <div class="head-actions">
          <?php if (!$modeEdit): ?>
            <a class="btn btn-secondary" href="?id=<?= $koleksiId ?>&edit=1">✎ Ubah Nama / Deskripsi</a>
          <?php endif; ?>
          <form method="post" onsubmit="return confirm('Hapus koleksi ini? Catatan aslinya tetap aman di katalog.');">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" value="<?= $koleksiId ?>">
            <button type="submit" class="btn-text danger">Hapus koleksi ini</button>
          </form>
        </div>
      </div>

      <h3 class="section-title"><?= $jumlahCatatan ?> Catatan dalam koleksi ini</h3>
      <div class="note-list">
        <?php if (empty($isiKoleksi)): ?>
          <div class="empty">Belum ada catatan di koleksi ini. Tambahkan dari daftar di bawah.</div>
        <?php else: ?>
          <?php foreach ($isiKoleksi as $i => $c): ?>
            <div class="note-row">
              <div class="reorder">
                <form method="post">
                  <input type="hidden" name="aksi" value="geser">
                  <input type="hidden" name="catatan_id" value="<?= (int) $c['id'] ?>">
                  <input type="hidden" name="arah" value="naik">
                  <button type="submit" class="btn-icon" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Naikkan urutan">▲</button>
                </form>
                <form method="post">
                  <input type="hidden" name="aksi" value="geser">
                  <input type="hidden" name="catatan_id" value="<?= (int) $c['id'] ?>">
                  <input type="hidden" name="arah" value="turun">
                  <button type="submit" class="btn-icon" <?= $i === $jumlahCatatan - 1 ? 'disabled' : '' ?> aria-label="Turunkan urutan">▼</button>
                </form>
              </div>
              <div class="info">
                <span class="tag"><?= htmlspecialchars($c['jurusan']) ?></span>
                <h4><?= htmlspecialchars($c['judul']) ?></h4>
                <p class="meta">oleh <?= htmlspecialchars($c['penyusun']) ?> · <?= htmlspecialchars($c['tipe']) ?></p>
              </div>
              <form method="post" onsubmit="return confirm('Keluarkan catatan ini dari koleksi?');">
                <input type="hidden" name="aksi" value="keluarkan_catatan">
                <input type="hidden" name="catatan_id" value="<?= (int) $c['id'] ?>">
                <button type="submit" class="btn-text danger">Keluarkan</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <h3 class="section-title">Tambahkan catatan ke koleksi</h3>
      <?php if (empty($catatanTersedia)): ?>
        <div class="empty">Semua catatan yang ada sudah masuk ke koleksi ini.</div>
      <?php else: ?>
        <form method="post" class="add-note-row">
          <input type="hidden" name="aksi" value="tambah_catatan">
          <div class="field">
            <label for="catatan_id">Pilih catatan</label>
            <select id="catatan_id" name="catatan_id" required>
              <option value="" disabled selected>-- pilih catatan --</option>
              <?php foreach ($catatanTersedia as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['judul']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">+ Tambah ke Koleksi</button>
        </form>
      <?php endif; ?>
    <?php endif; ?>

  </main>
</div>

<script>
function bukaSidebar(){ document.body.classList.add('sidebar-open'); }
function tutupSidebar(){ document.body.classList.remove('sidebar-open'); }
window.addEventListener('resize', function () { if (window.innerWidth > 900) tutupSidebar(); });
function bukaModal(){ document.body.classList.add('modal-open'); }
function tutupModal(){ document.body.classList.remove('modal-open'); }
</script>
</body>
</html>