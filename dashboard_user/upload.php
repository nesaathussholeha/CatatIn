<?php
declare(strict_types=1);

const APP_NAME = 'Notes Hub';
const PENGGUNA_INISIAL = 'SF';

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

$daftarJurusan  = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Elektro'];
$daftarKategori = ['Rangkuman', 'Soal Ujian', 'Modul'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unggah Catatan Baru — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="app">
    <!-- Header Topbar Mobile -->
    <header class="topbar">
        <button type="button" class="hamburger" id="tombol-menu" aria-label="Buka menu">
            <span></span><span></span><span></span>
        </button>
        <a class="logo-atas" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
    </header>
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <button type="button" class="tutup-menu" id="tutup-menu" aria-label="Tutup menu">✕</button>
        <a class="logo" href="dashboard.php"><span aria-hidden="true">📖</span> <?= APP_NAME ?></a>
        <nav aria-label="Menu utama">
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="upload.php" aria-current="page">Unggah Catatan</a></li>
                <li><a href="catatan-saya.php">Catatan Saya</a></li>
                <li><a href="#">Profil</a></li>
            </ul>
        </nav>
        <div class="sidebar-bawah">
            <a href="../auth/login.php" class="menu-keluar">Keluar</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="konten">
    <div class="top-bar-desktop">
        <div class="avatar" title="Akun saya"><?= e(PENGGUNA_INISIAL) ?></div>
    </div>

    <!-- Container Form -->
    <div class="card-form">
        <h2>Unggah catatan baru</h2>
        <form action="proses-upload.php" method="POST">
            
            <div class="form-group">
                <label for="judul">Judul catatan</label>
                <input type="text" id="judul" name="judul" placeholder="Rangkuman Aljabar Linear Bab 3" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="matkul">Mata kuliah</label>
                    <input type="text" id="matkul" name="matkul" placeholder="Aljabar Linear" required>
                </div>
                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select id="jurusan" name="jurusan" required>
                        <?php foreach ($daftarJurusan as $j): ?>
                            <option value="<?= e($j) ?>"><?= e($j) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Tipe berkas</label>
                <div class="pill-group">
                    <?php foreach ($daftarKategori as $index => $k): ?>
                        <label class="pill-btn">
                            <input type="radio" name="kategori" value="<?= e($k) ?>" <?= $index === 0 ? 'checked' : '' ?>>
                            <span><?= e($k) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi singkat</label>
                <input type="text" id="deskripsi" name="deskripsi" placeholder="Mencakup transformasi linear dan nilai eigen..." required>
            </div>

            <div class="form-group">
                <label for="tautan">Tautan berkas</label>
                <input type="text" id="tautan" name="tautan" placeholder="drive.google.com/..." required>
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-container">
                    <input type="checkbox" name="is_anonim" value="1">
                    Unggah sebagai anonim
                </label>
            </div>

            <button type="submit" class="btn-primary">Simpan catatan</button>
        </form>
    </div>
</main>
</div>

<script src="script.js"></script>
</body>
</html>