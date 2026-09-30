<?php
declare(strict_types=1);
session_start();

const APP_NAME = 'CatatIn';

/* ---------- Helper ---------- */
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
    return is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

/*
 * Data contoh katalog (statis, belum database).
 * Urutan kolom: id, judul, jurusan, kategori
 */
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

/* ---------- State awal (disimpan di sesi, belum database) ---------- */
if (!isset($_SESSION['folders'])) {
    $_SESSION['folders'] = [
        1 => ['id' => 1, 'nama' => 'Bahan UTS Pemrograman Web',   'notes' => [5, 6, 7]],
        2 => ['id' => 2, 'nama' => 'Rangkuman Bab 1–4 Kalkulus', 'notes' => [8, 9, 10, 11]],
        3 => ['id' => 3, 'nama' => 'Referensi Skripsi',          'notes' => [12, 13]],
    ];
    $_SESSION['folder_next'] = 4;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* ---------- Proses aksi: Create, Update, Delete ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        flash('Sesi berakhir. Muat ulang halaman lalu coba lagi.', 'error');
        pindah('playlist.php');
    }

    $aksi = (string) ($_POST['aksi'] ?? '');
    $fid  = (int) ($_POST['folder_id'] ?? 0);
    $nama = trim((string) ($_POST['nama'] ?? ''));

    switch ($aksi) {
        case 'buat':                                   // CREATE folder
            if ($nama === '' || mb_strlen($nama) > 60) {
                flash('Nama folder wajib diisi, maksimal 60 karakter.', 'error');
                pindah('playlist.php');
            }
            $baru = (int) $_SESSION['folder_next']++;
            $_SESSION['folders'][$baru] = ['id' => $baru, 'nama' => $nama, 'notes' => []];
            flash('Folder "' . $nama . '" dibuat.');
            pindah('playlist.php?f=' . $baru);

        case 'ubah_nama':                              // UPDATE nama folder
            if (!isset($_SESSION['folders'][$fid])) { pindah('playlist.php'); }
            if ($nama === '' || mb_strlen($nama) > 60) {
                flash('Nama folder wajib diisi, maksimal 60 karakter.', 'error');
                pindah('playlist.php?f=' . $fid);
            }
            $_SESSION['folders'][$fid]['nama'] = $nama;
            flash('Nama folder diperbarui.');
            pindah('playlist.php?f=' . $fid);

        case 'hapus':                                  // DELETE folder
            if (isset($_SESSION['folders'][$fid])) {
                unset($_SESSION['folders'][$fid]);
                flash('Folder dihapus. Catatan aslinya tetap ada di katalog.');
            }
            pindah('playlist.php');

        case 'tambah':                                 // UPDATE: tambah catatan ke folder
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

        case 'keluarkan':                              // UPDATE: keluarkan catatan dari folder
            if (isset($_SESSION['folders'][$fid])) {
                $nid = (int) ($_POST['catatan_id'] ?? 0);
                $_SESSION['folders'][$fid]['notes'] = array_values(
                    array_filter($_SESSION['folders'][$fid]['notes'], static fn($x) => $x !== $nid)
                );
                flash('Catatan dikeluarkan dari folder. Catatan aslinya tidak terhapus.');
            }
            pindah('playlist.php?f=' . $fid);
    }
    pindah('playlist.php');
}

/* ---------- Tampilan: READ ---------- */
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Koleksi Belajar <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="playlist.css">
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
                <li><a href="../dashboard_user/dashboard.php">Dashboard</a></li>
                <li><a href="unggah.php">Unggah Catatan</a></li>
                <li><a href="catatan-saya.php">Catatan Saya</a></li>
                <li><a href="playlist.php" aria-current="page">Koleksi Belajar</a></li>
                <li><a href="tugas.php">Tugas Belajar</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="../auth/login.php">Keluar</a></li>
            </ul>
        </nav>
    </aside>

    <main class="konten">
        <?php if ($flash): ?>
            <div class="toast toast-<?= e($flash['tipe']) ?>" role="status"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>

        <div class="judul-baris">
            <h1>Koleksi belajar saya</h1>
            <button type="button" class="tombol tombol-utama" data-buka="dlg-buat">+ Buat folder</button>
        </div>

        <?php if (!$folders): ?>
            <div class="kosong">
                <p><strong>Belum ada folder.</strong></p>
                <p>Buat folder pertama untuk mengelompokkan catatan yang ingin kamu simpan.</p>
                <button type="button" class="tombol tombol-utama" data-buka="dlg-buat">+ Buat folder</button>
            </div>
        <?php else: ?>
        <div class="koleksi">
            <nav class="daftar-folder" aria-label="Daftar folder">
                <?php foreach ($folders as $f): ?>
                    <div class="folder-item">
                        <a class="folder-kartu<?= $f['id'] === $aktifId ? ' aktif' : '' ?>"
                           href="playlist.php?f=<?= (int) $f['id'] ?>"
                           <?= $f['id'] === $aktifId ? 'aria-current="true"' : '' ?>>
                            <strong><span aria-hidden="true">📁</span> <?= e($f['nama']) ?></strong>
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

            <section class="panel" aria-labelledby="judul-folder">
                <div class="panel-kepala">
                    <h2 id="judul-folder">Isi folder: <?= e($folder['nama']) ?></h2>
                </div>

                <?php if (!$catatanFolder): ?>
                    <p class="kosong-kecil">Folder ini masih kosong. Tambahkan catatan dari katalog.</p>
                <?php else: ?>
                    <div class="tabel-bungkus">
                        <table class="tabel">
                            <thead>
                                <tr><th scope="col">Judul catatan</th><th scope="col">Jurusan</th><th scope="col"><span class="sr-only">Aksi</span></th></tr>
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

                <button type="button" class="tombol tambah-catatan" data-buka="dlg-tambah">+ Tambahkan catatan dari katalog</button>
            </section>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- Dialog: buat folder -->
<dialog id="dlg-buat" class="modal">
    <form method="post" class="modal-isi">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="buat">
        <h2>Buat folder</h2>
        <label for="nama-baru">Nama folder</label>
        <input type="text" id="nama-baru" name="nama" maxlength="60" required placeholder="Contoh: Bahan UAS Basis Data">
        <div class="modal-aksi">
            <button type="button" class="tombol" data-tutup>Batal</button>
            <button type="submit" class="tombol tombol-utama">Buat folder</button>
        </div>
    </form>
</dialog>

<?php if ($folder): ?>
<!-- Dialog: edit nama (folder_id dan nama diisi JS sesuai folder yang dipilih) -->
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
            <button type="submit" class="tombol tombol-utama">Simpan nama</button>
        </div>
    </form>
</dialog>

<!-- Dialog: hapus folder (folder_id dan nama diisi JS sesuai folder yang dipilih) -->
<dialog id="dlg-hapus" class="modal">
    <form method="post" class="modal-isi">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="folder_id" value="<?= (int) $folder['id'] ?>">
        <h2>Hapus folder?</h2>
        <p>Folder <strong data-isi-nama><?= e($folder['nama']) ?></strong> akan dihapus. Catatan asli di dalamnya tidak ikut terhapus.</p>
        <div class="modal-aksi">
            <button type="button" class="tombol" data-tutup>Batal</button>
            <button type="submit" class="tombol tombol-bahaya-penuh">Hapus folder</button>
        </div>
    </form>
</dialog>

<!-- Dialog: tambah catatan dari katalog -->
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
                <button type="submit" class="tombol tombol-utama">Tambahkan</button>
            </div>
        <?php endif; ?>
    </form>
</dialog>
<?php endif; ?>

<script src="playlist.js"></script>
</body>
</html>