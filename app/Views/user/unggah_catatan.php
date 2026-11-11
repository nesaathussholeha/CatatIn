<?php
/**
 * @var string $page_title
 * @var string $active_menu
 * @var string $user_name
 * @var string $user_role
 * @var string $user_initial
 * @var string $csrf
 * @var int $idForm
 * @var array $galat
 * @var array $input
 * @var array $daftarJurusan
 * @var array $daftarKategori
 * @var bool $modeEdit
 */

if (!function_exists('e')) {
    function e($teks): string
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title); ?> - CatatIn</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/unggah_catatan.css">
</head>

<body>
    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>CatatIn</span>
                    </div>

                    <button class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="<?= BASE_URL ?>/?page=dashboard"
                            class="nav-link <?= $active_menu === 'dashboard' ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/?page=unggah-catatan"
                            class="nav-link <?= $active_menu === 'unggah' ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon>
                            Unggah Catatan
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/?page=catatan-saya"
                            class="nav-link <?= $active_menu === 'catatan_saya' ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon>
                            Catatan Saya
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/?page=koleksi"
                            class="nav-link <?= $active_menu === 'koleksi' ? 'active' : ''; ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon>
                            Koleksi
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/?page=pengingat"
                            class="nav-link <?= $active_menu === 'pengingat' ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon>
                            Pengingat
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="<?= BASE_URL ?>/?page=logout" class="nav-link logout">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon>
                    Keluar
                </a>
            </div>
        </aside>

        <!-- Konten Utama -->
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
                        <span class="user-name"><?= e($user_name); ?></span>
                        <span class="user-role"><?= e($user_role); ?></span>
                    </div>

                    <div class="avatar">
                        <?= e($user_initial); ?>
                    </div>
                </div>
            </header>

            <main class="content-area">
                <form action="<?= BASE_URL ?>/?page=unggah-catatan" method="POST">
                    <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                    <input type="hidden" name="id" value="<?= (int) $idForm; ?>">

                    <div class="card-form">
                        <?php if (!empty($galat)): ?>
                            <div class="alert-galat" role="alert">
                                <ul>
                                    <?php foreach ($galat as $g): ?>
                                        <li><?= e($g); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="judul">Judul catatan</label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                maxlength="150"
                                value="<?= e($input['judul']); ?>"
                                placeholder="Rangkuman Aljabar Linear Bab 3"
                                required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="matkul">Mata kuliah</label>
                                <input
                                    type="text"
                                    id="matkul"
                                    name="matkul"
                                    maxlength="100"
                                    value="<?= e($input['matkul']); ?>"
                                    placeholder="Aljabar Linear"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="jurusan">Jurusan</label>
                                <select id="jurusan" name="jurusan" required>
                                    <?php foreach ($daftarJurusan as $j): ?>
                                        <option value="<?= e($j); ?>"
                                            <?= $input['jurusan'] === $j ? 'selected' : ''; ?>>
                                            <?= e($j); ?>
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
                                        <input
                                            type="radio"
                                            name="kategori"
                                            value="<?= e($k); ?>"
                                            <?= $input['kategori'] === $k ? 'checked' : ''; ?>>
                                        <span><?= e($k); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi singkat</label>
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                rows="3"
                                maxlength="255"
                                placeholder="Mencakup transformasi linear dan nilai eigen..."
                                required><?= e($input['deskripsi']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tautan">Tautan berkas</label>
                            <input
                                type="text"
                                id="tautan"
                                name="tautan"
                                maxlength="500"
                                value="<?= e($input['tautan']); ?>"
                                placeholder="drive.google.com/..."
                                required>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-container">
                                <input
                                    type="checkbox"
                                    name="is_anonim"
                                    value="1"
                                    <?= !empty($input['is_anonim']) ? 'checked' : ''; ?>>
                                Unggah sebagai anonim
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= BASE_URL ?>/?page=catatan-saya" class="btn-batal">
                            Batal
                        </a>

                        <button type="submit" class="btn-primary">
                            <?= $modeEdit ? 'Perbarui catatan' : 'Simpan catatan'; ?>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/js/unggah_catatan.js"></script>
</body>

</html>
