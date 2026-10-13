<?php
session_start();

$default_user = [
    'nama'     => 'Ryul',
    'jurusan'  => 'Informatika',
    'nim'      => '2141720001',
    'email'    => 'ryulmanis@student.ac.id',
    'no_hp'    => '1234567654',
    'password' => '',
    'foto'     => ''   // [UPLOAD] nama file foto profil di folder uploads/
];

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = $default_user;
} else {
    $_SESSION['user'] = array_merge($default_user, $_SESSION['user']);
}

$user  = $_SESSION['user'];
$pesan = '';
$tipe_pesan = 'success'; // success | error

function ambilInisial(string $nama): string
{
    $kata = preg_split('/\s+/', trim($nama));
    $inisial = '';

    foreach (array_slice($kata, 0, 2) as $k) {
        if ($k !== '') {
            $inisial .= mb_strtoupper(mb_substr($k, 0, 1));
        }
    }

    return $inisial !== '' ? $inisial : '?';
}

// [UPLOAD] Proses upload foto: $_FILES -> validasi -> move_uploaded_file()
// Return: [berhasil, nama_file_foto, pesan_error]
function prosesUploadFoto(array $file, string $fotoLama): array
{
    // Tidak memilih file = tidak ada perubahan foto
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, $fotoLama, ''];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, $fotoLama, 'Upload gagal (kode error ' . $file['error'] . ').'];
    }

    $ekstensi_boleh = ['jpg', 'jpeg', 'png'];
    $ukuran_maks    = 2 * 1024 * 1024; // 2MB
    $ekstensi       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensi_boleh, true)) {
        return [false, $fotoLama, 'Ekstensi tidak diizinkan (hanya jpg, jpeg, png).'];
    }

    if ($file['size'] > $ukuran_maks) {
        return [false, $fotoLama, 'Ukuran file melebihi 2MB.'];
    }

    // Jangan percaya ekstensi dari pengguna: cek isi file yang sebenarnya
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        return [false, $fotoLama, 'Isi file bukan gambar JPG/PNG yang valid.'];
    }

    // Pastikan folder tujuan ada dan bisa ditulis
    $folder = __DIR__ . '/uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    if (!is_writable($folder)) {
        return [false, $fotoLama, 'Folder uploads tidak bisa ditulis.'];
    }

    // Nama file diganti agar unik dan aman
    $nama_baru = uniqid('foto_') . '.' . $ekstensi;

    if (!move_uploaded_file($file['tmp_name'], $folder . $nama_baru)) {
        return [false, $fotoLama, 'Gagal memindahkan file ke folder uploads.'];
    }

    // Hapus foto lama agar folder tidak penuh
    if ($fotoLama !== '' && file_exists($folder . basename($fotoLama))) {
        unlink($folder . basename($fotoLama));
    }

    return [true, $nama_baru, ''];
}

// [UPLOAD] Hapus foto profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_foto'])) {
    $file_foto = __DIR__ . '/uploads/' . basename($user['foto']);

    if ($user['foto'] !== '' && file_exists($file_foto)) {
        unlink($file_foto);
    }

    $_SESSION['user']['foto'] = '';
    $user = $_SESSION['user'];

    $pesan = 'Foto profil berhasil dihapus.';
    $tipe_pesan = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hapus_foto'])) {
    $nama     = htmlspecialchars(trim($_POST['nama'] ?? ''));
    $jurusan  = htmlspecialchars(trim($_POST['jurusan'] ?? ''));
    $nim      = htmlspecialchars(trim($_POST['nim'] ?? ''));
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''));
    $no_hp    = htmlspecialchars(trim($_POST['no_hp'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($nama === '' || $jurusan === '') {
        $pesan = 'Nama dan jurusan tidak boleh kosong.';
        $tipe_pesan = 'error';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = 'Format email tidak valid.';
        $tipe_pesan = 'error';
    } else {
        // [UPLOAD] proses foto dulu; kalau gagal, perubahan lain tidak disimpan
        [$upload_ok, $nama_foto, $pesan_upload] = prosesUploadFoto(
            $_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE],
            $user['foto']
        );

        if (!$upload_ok) {
            $pesan = $pesan_upload;
            $tipe_pesan = 'error';
        } else {
            $_SESSION['user']['nama']    = $nama;
            $_SESSION['user']['jurusan'] = $jurusan;
            $_SESSION['user']['nim']     = $nim;
            $_SESSION['user']['email']   = $email;
            $_SESSION['user']['no_hp']   = $no_hp;
            $_SESSION['user']['foto']    = $nama_foto; // [UPLOAD]

            if ($password !== '') {
                $_SESSION['user']['password'] = $password;
            }

            $user = $_SESSION['user'];

            $pesan = 'Perubahan profil berhasil disimpan.';
            $tipe_pesan = 'success';
        }
    }
}

$inisial = ambilInisial($user['nama']);

// [UPLOAD] URL foto (kosong jika belum ada / file hilang)
$foto_url = '';
if ($user['foto'] !== '' && file_exists(__DIR__ . '/uploads/' . basename($user['foto']))) {
    $foto_url = 'uploads/' . basename($user['foto']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <header class="logo">
            <svg class="logo-icon" width="26" height="26" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <strong>CatatIn</strong>

            <!-- Tombol tutup (hanya tampil di HP/tab) -->
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </header>

        <nav class="navigation">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="unggah-catatan.php" class="nav-item">Unggah Catatan</a>
            <a href="catatan-saya.php" class="nav-item">Catatan Saya</a>
            <a href="koleksi-belajar.php" class="nav-item">Koleksi Belajar</a>
            <a href="tugas-belajar.php" class="nav-item">Tugas Belajar</a>
            <a href="profile.php" class="nav-item active">Profil</a>
        </nav>
        <a href="logout.php" class="nav-item logout">Keluar</a>
    </aside>

    <!-- Overlay gelap di belakang sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <!-- TOP BAR -->
        <header class="topbar">
            <button type="button" class="menu-toggle" id="menuToggle"
                    aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <span class="top-avatar">
                <?php if ($foto_url !== ''): ?><!-- [UPLOAD] -->
                    <img src="<?php echo htmlspecialchars($foto_url); ?>" alt="Foto profil">
                <?php else: ?>
                    <?php echo $inisial; ?>
                <?php endif; ?>
            </span>
        </header>

        <!-- ISI HALAMAN -->
        <section class="content">
            <h1>Profil saya</h1>

            <!-- PESAN -->
            <?php if ($pesan !== ''): ?>
                <p class="<?php echo $tipe_pesan === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo $pesan; ?>
                </p>
            <?php endif; ?>

            <!-- STATISTIK -->
            <section class="statistics">
                <article class="stat-card">
                    <strong class="stat-number">226</strong>
                    <span class="stat-label">Total upvote diterima</span>
                </article>

                <article class="stat-card">
                    <strong class="stat-number">7</strong>
                    <span class="stat-label">Catatan diunggah</span>
                </article>

                <article class="stat-card badge-card">
                    <strong class="badge-title">🏅 Top<br>Contributor</strong>
                    <span class="stat-label">Badge reputasi</span>
                </article>
            </section>

            <!-- FORM PROFIL -->
            <section class="profile-card">
                <div class="profile-card-header">
                    <span class="profile-avatar-lg">
                        <?php if ($foto_url !== ''): ?><!-- [UPLOAD] -->
                            <img src="<?php echo htmlspecialchars($foto_url); ?>" alt="Foto profil">
                        <?php else: ?>
                            <?php echo $inisial; ?>
                        <?php endif; ?>
                    </span>
                    <div>
                        <strong class="profile-name"><?php echo htmlspecialchars($user['nama']); ?></strong>
                        <span class="profile-sub"><?php echo htmlspecialchars($user['jurusan']); ?></span>
                    </div>
                </div>

                <!-- [UPLOAD] enctype="multipart/form-data" wajib agar file terkirim -->
                <form action="profile.php" method="POST" id="profileForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- NAMA -->
                        <p class="form-group">
                            <label for="nama">Nama lengkap</label>
                            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                        </p>

                        <!-- JURUSAN -->
                        <p class="form-group">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" id="jurusan" name="jurusan" value="<?php echo htmlspecialchars($user['jurusan']); ?>" required>
                        </p>

                        <!-- NIM -->
                        <p class="form-group">
                            <label for="nim">NIM / Nomor Induk</label>
                            <input type="text" id="nim" name="nim" value="<?php echo htmlspecialchars($user['nim']); ?>" placeholder="Contoh: 2141720001">
                        </p>

                        <!-- EMAIL -->
                        <p class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="nama@student.ac.id">
                        </p>

                        <!-- NO HP -->
                        <p class="form-group">
                            <label for="no_hp">Nomor HP</label>
                            <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($user['no_hp']); ?>" placeholder="08xxxxxxxxxx">
                        </p>

                        <!-- PASSWORD -->
                        <p class="form-group">
                            <label for="password">Ubah Password</label>
                            <span class="password-wrapper">
                                <input type="password" id="password" name="password" placeholder="Masukkan password baru">
                                <button type="button" class="show-password" id="showPassword" aria-label="Tampilkan password">👁</button>
                            </span>
                        </p>

                        <!-- [UPLOAD] FOTO PROFIL -->
                        <p class="form-group form-group-full">
                            <label for="photo">Foto profil</label>
                            <input type="file" id="photo" name="foto" accept=".jpg,.jpeg,.png">
                            <small class="field-hint">Format JPG/JPEG/PNG, maksimal 2MB.</small>
                            <img id="photoPreview" class="photo-preview" alt="Pratinjau foto" hidden>
                            <small id="photoError" class="field-error" hidden></small>
                            <?php if ($foto_url !== ''): ?>
                                <button type="submit" form="hapusFotoForm" class="btn btn-danger btn-small"
                                        onclick="return confirm('Hapus foto profil?');">Hapus foto</button>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- BUTTON -->
                    <p class="button-group">
                        <button type="submit" class="btn btn-primary" id="saveButton">Simpan perubahan</button>
                        <button type="button" class="btn btn-danger" id="deactivateButton">Nonaktifkan akun</button>
                    </p>
                </form>

                <!-- [UPLOAD] form khusus hapus foto -->
                <form action="profile.php" method="POST" id="hapusFotoForm" hidden>
                    <input type="hidden" name="hapus_foto" value="1">
                </form>
            </section>
        </section>
    </main>

    <!-- JAVASCRIPT -->
    <script src="profile.js"></script>
</body>
</html>