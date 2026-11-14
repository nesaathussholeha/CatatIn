<?php
/**
 * View murni: hanya menampilkan data dari CatatanController::form().
 * Variabel di bawah dikirim oleh controller lewat render() + extract().
 *
 * @var string $page_title
 * @var string $active_menu
 * @var string $sidebar       HTML sidebar dari layouts/sidebar.php
 * @var string $user_name
 * @var string $user_role
 * @var string $user_initial
 * @var string $csrf
 * @var string[] $galat
 * @var int $idForm
 * @var array<string, mixed> $input
 * @var bool $modeEdit
 * @var string[] $daftarJurusan
 * @var string[] $daftarKategori
 * @var array<string, string> $daftarVisibilitas
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - <?= APP_NAME ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/layout_user.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/unggah_catatan.css">
</head>

<body>
    <div class="app-container">

        <?= $sidebar ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Buka Menu">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= e($page_title) ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name) ?></span>
                        <span class="user-role"><?= e($user_role) ?></span>
                    </div>
                    <div class="avatar"><?= e($user_initial) ?></div>
                </div>
            </header>

            <main class="content-area">
                <form action="<?= BASE_URL ?>/?page=unggah-catatan" method="POST">
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int) $idForm ?>">

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
                            <input type="text" id="judul" name="judul" maxlength="150"
                                value="<?= e($input['judul']) ?>"
                                placeholder="Rangkuman Aljabar Linear Bab 3" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="matkul">Mata kuliah</label>
                                <input type="text" id="matkul" name="matkul" maxlength="100"
                                    value="<?= e($input['matkul']) ?>" placeholder="Aljabar Linear" required>
                            </div>
                            <div class="form-group">
                                <label for="jurusan">Jurusan</label>
                                <select id="jurusan" name="jurusan" required>
                                    <?php foreach ($daftarJurusan as $j): ?>
                                        <option value="<?= e($j) ?>" <?= $input['jurusan'] === $j ? 'selected' : '' ?>><?= e($j) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <span class="label-teks">Tipe berkas</span>
                            <div class="pill-group">
                                <?php foreach ($daftarKategori as $k): ?>
                                    <label class="pill-btn">
                                        <input type="radio" name="kategori" value="<?= e($k) ?>" <?= $input['kategori'] === $k ? 'checked' : '' ?>>
                                        <span><?= e($k) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <span class="label-teks">Visibilitas</span>
                            <div class="pill-group dua-kolom">
                                <?php foreach ($daftarVisibilitas as $nilai => $label): ?>
                                    <label class="pill-btn">
                                        <input type="radio" name="visibilitas" value="<?= e($nilai) ?>" <?= $input['visibilitas'] === $nilai ? 'checked' : '' ?>>
                                        <span><?= e($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <small class="bantuan-teks">Pribadi: hanya kamu yang bisa melihat. Publik: bisa dilihat pengguna lain setelah catatan terbit.</small>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi singkat</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="255"
                                placeholder="Mencakup transformasi linear dan nilai eigen..." required><?= e($input['deskripsi']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tautan">Tautan berkas</label>
                            <input type="text" id="tautan" name="tautan" maxlength="500"
                                value="<?= e($input['tautan']) ?>" placeholder="drive.google.com/..." required>
                        </div>

                        <div class="form-group checkbox-group" id="grup-anonim">
                            <label class="checkbox-container">
                                <input type="checkbox" id="is_anonim" name="is_anonim" value="1" <?= $input['is_anonim'] ? 'checked' : '' ?>>
                                Unggah sebagai anonim (hanya berlaku untuk catatan publik)
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= BASE_URL ?>/?page=catatan-saya" class="btn-batal">Batal</a>
                        <button type="submit" class="btn-primary"><?= $modeEdit ? 'Perbarui catatan' : 'Simpan catatan' ?></button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/layout_user/script.js"></script>
    <script src="<?= BASE_URL ?>/assets/upload/upload.js"></script>
</body>

</html>