<?php

$galat = $galat ?? [];
$input = $input ?? [];

$daftarJurusan = $daftarJurusan ?? [
    'Informatika',
    'Sistem Informasi',
    'Teknik Komputer',
    'Elektro'
];

$daftarKategori = $daftarKategori ?? [
    'Rangkuman',
    'Soal Ujian',
    'Modul'
];

$modeEdit = $modeEdit ?? false;
$idForm = $idForm ?? 0;

$page_title = $page_title ?? 'Unggah Catatan';
$user_name = $user_name ?? 'Username';
$user_role = $user_role ?? 'Role';
$user_initial = $user_initial ?? 'U';

$csrf = $csrf ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> - CatatIn</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="../layout_user/style.css">
    <link rel="stylesheet" href="../../upload/upload.css">
</head>

<body>

    <div class="app-container">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">

            <div>
                <div class="sidebar-header">
                    <div class="brand">
                        <span>CatatIn</span>
                    </div>

                    <button class="close-sidebar-btn" id="closeSidebar">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="../dashboard_user/dashboard.php" class="nav-link">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="../../upload/upload.php" class="nav-link active">
                            Unggah Catatan
                        </a>
                    </li>

                    <li>
                        <a href="../../catatan_saya/catatan_saya.php" class="nav-link">
                            Catatan Saya
                        </a>
                    </li>

                    <li>
                        <a href="../playlist/playlist.php" class="nav-link">
                            Koleksi
                        </a>
                    </li>

                    <li>
                        <a href="../Pengingat/tugas.php" class="nav-link">
                            Tugas
                        </a>
                    </li>

                    <li>
                        <a href="../profile/profile.php" class="nav-link">
                            Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="../logout.php" class="nav-link logout">
                    Keluar
                </a>
            </div>

        </aside>

        <div class="main-wrapper">

            <header class="top-header">

                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>

                    <h1 class="page-title">
                        <?= e($page_title) ?>
                    </h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name) ?></span>
                        <span class="user-role"><?= e($user_role) ?></span>
                    </div>

                    <div class="avatar">
                        <?= e($user_initial) ?>
                    </div>
                </div>

            </header>

            <main class="content-area">

                <form action="upload.php" method="POST">

                    <input type="hidden" name="csrf"
                        value="<?= e($_SESSION['csrf']) ?>">

                    <input type="hidden" name="id"
                        value="<?= $idForm ?>">

                    <div class="card-form">

                        <?php if ($galat !== []): ?>
                            <div class="alert-galat" role="alert">
                                <ul>
                                    <?php foreach ($galat as $g): ?>
                                        <li><?= e($g) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="judul">Judul catatan</label>

                            <input type="text" id="judul" name="judul"
                                maxlength="150"
                                value="<?= e($input['judul']) ?>"
                                placeholder="Rangkuman Aljabar Linear Bab 3"
                                required>
                        </div>

                        <div class="form-row">

                            <div class="form-group">
                                <label for="matkul">Mata kuliah</label>

                                <input type="text" id="matkul" name="matkul"
                                    maxlength="100"
                                    value="<?= e($input['matkul']) ?>"
                                    placeholder="Aljabar Linear"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="jurusan">Jurusan</label>

                                <select id="jurusan" name="jurusan" required>

                                    <?php foreach ($daftarJurusan as $j): ?>
                                        <option value="<?= e($j) ?>"
                                            <?= $input['jurusan'] === $j ? 'selected' : '' ?>>
                                            <?= e($j) ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <span class="label-teks">Tipe berkas</span>

                            <div class="pill-group">

                                <?php foreach ($daftarKategori as $k): ?>
                                    <label class="pill-btn">

                                        <input type="radio" name="kategori"
                                            value="<?= e($k) ?>"
                                            <?= $input['kategori'] === $k ? 'checked' : '' ?>>

                                        <span><?= e($k) ?></span>

                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi singkat</label>

                            <textarea id="deskripsi" name="deskripsi"
                                rows="3" maxlength="255"
                                placeholder="Masukkan deskripsi catatan..."
                                required><?= e($input['deskripsi']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tautan">Tautan berkas</label>

                            <input type="text" id="tautan" name="tautan"
                                maxlength="500"
                                value="<?= e($input['tautan']) ?>"
                                placeholder="drive.google.com/..."
                                required>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-container">

                                <input type="checkbox" name="is_anonim"
                                    value="1"
                                    <?= $input['is_anonim'] ? 'checked' : '' ?>>

                                Unggah sebagai anonim

                            </label>
                        </div>

                    </div>

                    <div class="form-actions">

                        <a href="../catatan_saya/catatan_saya.php"
                            class="btn-batal">
                            Batal
                        </a>

                        <button type="submit" class="btn-primary">
                            <?= $modeEdit ? 'Perbarui catatan' : 'Simpan catatan' ?>
                        </button>

                    </div>

                </form>

            </main>
        </div>
    </div>

    <script src="../layout_user/script.js"></script>

</body>

</html>