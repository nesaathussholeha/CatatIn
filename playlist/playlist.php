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

// --- KONSTANTA & KONFIGURASI HALAMAN ---
const APP_NAME = 'CatatIn';
$page_title  = "Koleksi belajar saya";
$active_menu = "koleksi";

$user_name    = $_SESSION['user_name'] ?? "Pengguna";
$user_role    = $_SESSION['user_role'] ?? "User";
$user_initial = strtoupper(substr($user_name, 0, 2));

// --- HELPER FUNCTIONS ---
function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function pindah(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $pesan, string $tipe = 'ok'): void
{
    $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function csrf_valid(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf'] ?? '') . '">';
}

function katalog(): array
{
    $baris = [
        [1,  'Rangkuman Struktur Data — Pohon & Graf',   'Informatika',      'Rangkuman'],
        [2,  'Kumpulan Soal UAS Basis Data 2025',        'Sistem Informasi', 'Soal Ujian'],
        [3,  'Modul Praktikum Rangkaian Digital',        'Elektro',          'Modul'],
        [4,  'Rangkuman Pemrograman Web — Sesi 1–7',     'Informatika',      'Rangkuman'],
        [5,  'Rangkuman Struktur Data',                  'Informatika',      'Rangkuman'],
        [6,  'Modul Praktikum HTML/CSS',                 'Informatika',      'Modul'],
        [7,  'Ringkasan Pemrograman Web — Sesi 8–14',    'Informatika',      'Rangkuman'],
        [8,  'Rangkuman Kalkulus — Limit & Kontinuitas', 'Informatika',      'Rangkuman'],
        [9,  'Rangkuman Kalkulus — Turunan',             'Informatika',      'Rangkuman'],
        [10, 'Rangkuman Kalkulus — Aplikasi Turunan',    'Informatika',      'Rangkuman'],
        [11, 'Latihan Soal Kalkulus Bab 1–4',            'Informatika',      'Soal Ujian'],
        [12, 'Panduan Penulisan Skripsi',                'Informatika',      'Modul'],
        [13, 'Contoh Daftar Pustaka',                    'Informatika',      'Modul'],
    ];

    $hasil = [];
    foreach ($baris as [$id, $judul, $jurusan, $kategori]) {
        $hasil[$id] = compact('id', 'judul', 'jurusan', 'kategori');
    }
    return $hasil;
}

function find_note(int $id): ?array
{
    return katalog()[$id] ?? null;
}

// Inisialisasi Data Folder di Session jika belum ada
if (!isset($_SESSION['folders'])) {
    $_SESSION['folders'] = [
        1 => ['id' => 1, 'nama' => 'Bahan UTS Pemrograman WEB',   'notes' => [5, 6, 7]],
        2 => ['id' => 2, 'nama' => 'Rangkuman Bab 1–4 Kalkulus', 'notes' => [8, 9, 10, 11]],
        3 => ['id' => 3, 'nama' => 'Referensi Skripsi',          'notes' => [12, 13]],
    ];
    $_SESSION['folder_next'] = 4;
}

// Token CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// --- PENANGANAN FORM POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        flash('Sesi berakhir. Muat ulang halaman lalu coba lagi.', 'error');
        pindah('playlist.php');
    }

    $aksi = (string) ($_POST['aksi'] ?? '');
    $fid  = (int) ($_POST['folder_id'] ?? 0);
    $nama = trim((string) ($_POST['nama'] ?? ''));

    switch ($aksi) {
        case 'buat':
            if ($nama === '' || mb_strlen($nama) > 60) {
                flash('Nama folder wajib diisi, maksimal 60 karakter.', 'error');
                pindah('playlist.php');
            }
            $baru = (int) $_SESSION['folder_next']++;
            $_SESSION['folders'][$baru] = ['id' => $baru, 'nama' => $nama, 'notes' => []];
            flash('Folder "' . $nama . '" dibuat.');
            pindah('playlist.php?f=' . $baru);

        case 'ubah_nama':
            if (!isset($_SESSION['folders'][$fid])) { pindah('playlist.php'); }
            if ($nama === '' || mb_strlen($nama) > 60) {
                flash('Nama folder wajib diisi, maksimal 60 karakter.', 'error');
                pindah('playlist.php?f=' . $fid);
            }
            $_SESSION['folders'][$fid]['nama'] = $nama;
            flash('Nama folder diperbarui.');
            pindah('playlist.php?f=' . $fid);

        case 'hapus':
            if (isset($_SESSION['folders'][$fid])) {
                unset($_SESSION['folders'][$fid]);
                flash('Folder dihapus. Catatan aslinya tetap ada di katalog.');
            }
            pindah('playlist.php');

        case 'tambah':
            if (!isset($_SESSION['folders'][$fid])) { pindah('playlist.php'); }
            $dipilih = array_map('intval', (array) ($_POST['catatan'] ?? []));
            $jumlah  = 0;
            foreach ($dipilih as $nid) {
                if (find_note($nid) && !in_array($nid, $_SESSION['folders'][$fid]['notes'], true)) {
                    $_SESSION['folders'][$fid]['notes'][] = $nid;
                    $jumlah++;
                }
            }
            flash($jumlah > 0 ? "$jumlah catatan ditambahkan ke folder." : 'Belum ada catatan yang dipilih.', $jumlah > 0 ? 'ok' : 'error');
            pindah('playlist.php?f=' . $fid);

        case 'keluarkan':
            if (isset($_SESSION['folders'][$fid])) {
                $nid = (int) ($_POST['catatan_id'] ?? 0);
                $_SESSION['folders'][$fid]['notes'] = array_values(
                    array_filter($_SESSION['folders'][$fid]['notes'], static fn($x) => $x !== $nid)
                );
                flash('Catatan dikeluarkan dari folder.');
            }
            pindah('playlist.php?f=' . $fid);
    }
    pindah('playlist.php');
}

// Data Pendukung Tampilan
$folders = $_SESSION['folders'];
$aktifId = (int) ($_GET['f'] ?? 0);
if (!isset($folders[$aktifId])) {
    $aktifId = (int) (array_key_first($folders) ?? 0);
}
$folder = $folders[$aktifId] ?? null;

$catatanFolder = [];
$tersedia      = [];
if ($folder) {
    foreach ($folder['notes'] as $nid) {
        if ($n = find_note($nid)) { $catatanFolder[] = $n; }
    }
    $tersedia = array_filter(katalog(), static fn($n) => !in_array($n['id'], $folder['notes'], true));
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> — <?= APP_NAME ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="playlist.css">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">
            <div>
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span><?= APP_NAME ?></span>
                    </div>
                    <button class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="../dashboard_user/dashboard.php" class="nav-link <?= ($active_menu === 'dashboard') ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="../dashboard_user/upload.php" class="nav-link <?= ($active_menu === 'unggah') ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon> Unggah Catatan
                        </a>
                    </li>
                    <li>
                        <a href="../dashboard_user/catatan_saya.php" class="nav-link <?= ($active_menu === 'catatan_saya') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon> Catatan Saya
                        </a>
                    </li>
                    <li>
                        <a href="../playlist/playlist.php" class="nav-link <?= ($active_menu === 'koleksi') ? 'active' : ''; ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon> Koleksi
                        </a>
                    </li>
                    <li>
                        <a href="../pengingat/tugas.php" class="nav-link <?= ($active_menu === 'tugas') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon> Tugas
                        </a>
                    </li>
                    <li>
                        <a href="../profile.php" class="nav-link <?= ($active_menu === 'profil') ? 'active' : ''; ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon> Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href=".../login.php" class="nav-link logout">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon> Keluar
                </a>
            </div>
        </aside>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= e($page_title); ?></h1>
                </div>

              <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name) ?></span>
                        <span class="user-role"><?= e($user_role) ?></span>
                    </div>
                    <div class="avatar"><?= e($user_initial) ?></div>
                </div>
            </header>
            </header>

            <main class="content-area">
                <?php if ($flash): ?>
                    <div class="toast toast-<?= e($flash['tipe']) ?>" role="status"><?= e($flash['pesan']) ?></div>
                <?php endif; ?>

                <?php if (!$folders): ?>
                    <div class="kosong">
                        <p><strong>Belum ada folder.</strong></p>
                        <p>Buat folder pertama untuk mengelompokkan catatan yang ingin kamu simpan.</p>
                        <button type="button" class="tombol tombol-utama" data-buka="dlg-buat">+ Buat folder</button>
                    </div>
                <?php else: ?>
                    <div class="koleksi">
                        <div class="kolom-folder">
                            <nav class="daftar-folder" aria-label="Daftar folder">
                                <?php foreach ($folders as $f): ?>
                                    <div class="folder-item">
                                        <a class="folder-kartu<?= $f['id'] === $aktifId ? ' aktif' : '' ?>"
                                           href="playlist.php?f=<?= (int) $f['id'] ?>">
                                            <strong><?= e($f['nama']) ?></strong>
                                            <small><?= count($f['notes']) ?> catatan</small>
                                        </a>

                                        <button type="button" class="menu-titik"
                                                aria-label="Opsi folder <?= e($f['nama']) ?>"
                                                aria-haspopup="true" aria-expanded="false">
                                            <span aria-hidden="true">⋮</span>
                                        </button>
                                        <div class="menu-folder" hidden>
                                            <button type="button" data-buka="dlg-ubah"
                                                    data-folder-id="<?= (int) $f['id'] ?>"
                                                    data-folder-nama="<?= e($f['nama']) ?>">Edit nama</button>
                                            <button type="button" class="bahaya" data-buka="dlg-hapus"
                                                    data-folder-id="<?= (int) $f['id'] ?>"
                                                    data-folder-nama="<?= e($f['nama']) ?>">Hapus folder</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </nav>

                            <div class="area-buat-folder">
                                <button type="button" class="tombol tombol-utama-biru" data-buka="dlg-buat">+ Buat folder</button>
                            </div>
                        </div>

                        <section class="panel" aria-labelledby="judul-folder">
                            <div class="panel-kepala">
                                <h2 id="judul-folder">Isi Folder: <?= e($folder['nama']) ?></h2>
                            </div>

                            <?php if (!$catatanFolder): ?>
                                <p class="kosong-kecil">Folder ini masih kosong. Tambahkan catatan dari katalog.</p>
                            <?php else: ?>
                                <div class="tabel-bungkus">
                                    <table class="tabel">
                                        <thead>
                                            <tr>
                                                <th scope="col">Judul catatan</th>
                                                <th scope="col">Jurusan</th>
                                                <th scope="col"><span class="sr-only">Aksi</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($catatanFolder as $n): ?>
                                            <tr>
                                                <td><?= e($n['judul']) ?></td>
                                                <td><?= e($n['jurusan']) ?></td>
                                                <td class="kolom-aksi">
                                                    <form method="post" data-konfirm="Keluarkan catatan ini dari folder?">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="aksi" value="keluarkan">
                                                        <input type="hidden" name="folder_id" value="<?= (int) $folder['id'] ?>">
                                                        <input type="hidden" name="catatan_id" value="<?= (int) $n['id'] ?>">
                                                        <button type="submit" class="tombol-teks-bahaya">Keluarkan</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <div class="area-tambah-catatan">
                                <button type="button" class="tombol-outline-pill" data-buka="dlg-tambah">+ Tambahkan Catatan dari katalog</button>
                            </div>
                        </section>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modals -->
    <dialog id="dlg-buat" class="modal">
        <form method="post" class="modal-isi">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="buat">
            <h2>Buat folder</h2>
            <label for="nama-baru">Nama folder</label>
            <input type="text" id="nama-baru" name="nama" maxlength="60" required placeholder="Contoh: Bahan UAS Basis Data">
            <div class="modal-aksi">
                <button type="button" class="tombol" data-tutup>Batal</button>
                <button type="submit" class="tombol tombol-utama-biru">Buat folder</button>
            </div>
        </form>
    </dialog>

    <?php if ($folder): ?>
    <dialog id="dlg-ubah" class="modal">
        <form method="post" class="modal-isi">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="ubah_nama">
            <input type="hidden" name="folder_id" value="<?= (int) $folder['id'] ?>">
            <h2>Edit nama folder</h2>
            <label for="nama-ubah">Nama folder</label>
            <input type="text" id="nama-ubah" name="nama" maxlength="60" required value="<?= e($folder['nama']) ?>">
            <div class="modal-aksi">
                <button type="button" class="tombol" data-tutup>Batal</button>
                <button type="submit" class="tombol tombol-utama-biru">Simpan nama</button>
            </div>
        </form>
    </dialog>

    <dialog id="dlg-hapus" class="modal">
        <form method="post" class="modal-isi">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="folder_id" value="<?= (int) $folder['id'] ?>">
            <h2>Hapus folder?</h2>
            <p>Folder <strong data-isi-nama><?= e($folder['nama']) ?></strong> akan dihapus.</p>
            <div class="modal-aksi">
                <button type="button" class="tombol" data-tutup>Batal</button>
                <button type="submit" class="tombol tombol-bahaya-penuh">Hapus folder</button>
            </div>
        </form>
    </dialog>

    <dialog id="dlg-tambah" class="modal">
        <form method="post" class="modal-isi">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="folder_id" value="<?= (int) $folder['id'] ?>">
            <h2>Tambahkan catatan</h2>
            <?php if (!$tersedia): ?>
                <p>Semua catatan di katalog sudah ada di folder ini.</p>
                <div class="modal-aksi"><button type="button" class="tombol" data-tutup>Tutup</button></div>
            <?php else: ?>
                <ul class="pilih-catatan">
                    <?php foreach ($tersedia as $n): ?>
                        <li>
                            <label>
                                <input type="checkbox" name="catatan[]" value="<?= (int) $n['id'] ?>">
                                <span><?= e($n['judul']) ?><small><?= e($n['jurusan']) ?> · <?= e($n['kategori']) ?></small></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="modal-aksi">
                    <button type="button" class="tombol" data-tutup>Batal</button>
                    <button type="submit" class="tombol tombol-utama-biru">Tambahkan</button>
                </div>
            <?php endif; ?>
        </form>
    </dialog>
    <?php endif; ?>

    <script src="playlist.js"></script>
</body>

</html>