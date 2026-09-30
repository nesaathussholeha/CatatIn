<?php
session_start();

$default_user = [
    'nama'     => 'Ryul',
    'jurusan'  => 'Informatika',
    'nim'      => '2141720001',
    'email'    => 'ryulmanis@student.ac.id',
    'no_hp'    => '1234567654',
    'password' => ''
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $_SESSION['user']['nama']    = $nama;
        $_SESSION['user']['jurusan'] = $jurusan;
        $_SESSION['user']['nim']     = $nim;
        $_SESSION['user']['email']   = $email;
        $_SESSION['user']['no_hp']   = $no_hp;

        if ($password !== '') {
            $_SESSION['user']['password'] = $password;
        }

        $user = $_SESSION['user'];

        $pesan = 'Perubahan profil berhasil disimpan.';
        $tipe_pesan = 'success';
    }
}

$inisial = ambilInisial($user['nama']);
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

    <!-- CSS -->
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <header class="logo">
            <span class="logo-icon">▣</span>
            <strong>CatatIn</strong>
        </header>
        <nav class="navigation">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="unggah-catatan.php" class="nav-item">Unggah Catatan</a>
            <a href="catatan-saya.php" class="nav-item">Catatan Saya</a>
            <a href="koleksi-belajar.php" class="nav-item">Koleksi Belajar</a>
            <a href="tugas-belajar.php" class="nav-item">Tugas Belajar</a>
            <a href="profile.php" class="nav-item active">Profil</a>
            <a href="logout.php" class="nav-item logout">Keluar</a>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <!-- TOP BAR -->
        <header class="topbar">
            <span class="top-avatar">
                <?php echo $inisial; ?>
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
                    <span class="profile-avatar-lg"><?php echo $inisial; ?></span>
                    <div>
                        <strong class="profile-name"><?php echo htmlspecialchars($user['nama']); ?></strong>
                        <span class="profile-sub"><?php echo htmlspecialchars($user['jurusan']); ?></span>
                    </div>
                </div>

                <form action="profile.php" method="POST" id="profileForm">
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
                    </div>

                    <!-- BUTTON -->
                    <p class="button-group">
                        <button type="submit" class="btn btn-primary" id="saveButton">Simpan perubahan</button>
                        <button type="button" class="btn btn-danger" id="deactivateButton">Nonaktifkan akun</button>
                    </p>
                </form>
            </section>
        </section>
    </main>

    <!-- JAVASCRIPT -->
    <script src="profile.js"></script>
</body>
</html>