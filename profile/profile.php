<?php
session_start();

/* ---------- Data demo (disimpan di session, tanpa database) ---------- */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['user'])) {
    $_SESSION['user'] = [
        'nama'     => 'Ryul',
        'jurusan'  => 'Pendidikan Teknologi Informasi',
        'nim'      => '12345678909876',
        'email'    => 'ryulmania@gmail.com',
        'hp'       => '087657621234',
        'role'     => 'Mahasiswa',
        'foto'     => '',
        'password' => password_hash('password123', PASSWORD_DEFAULT), 
    ];
}
$stats = ['upvote' => 226, 'catatan' => 7, 'badge' => 'Top Contributor'];

/* ---------- Helper ---------- */
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function icon($name, $size = 20) {
    $p = [
        'like'      => '<path d="M7 11v10H3V11zM7 11l4-8a2 2 0 0 1 2 2v4h6a2 2 0 0 1 2 2.3l-1.4 7A2 2 0 0 1 17.7 20H7"/>',
        'share'     => '<path d="M12 15V3m-4 4 4-4 4 4"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/>',
        'camera'    => '<path d="M3 8a2 2 0 0 1 2-2h2l1.5-2h7L17 6h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/>',
        'save'      => '<path d="M5 3h11l3 3v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M7 3v5h8V3M7 21v-7h10v7"/>',
        'lock'      => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'eye'       => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off'   => '<path d="M3 3l18 18M10.6 5.1A9.7 9.7 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.7 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 4.4-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
        'check'     => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'alert'     => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5v.01"/>',
    ];
    return '<svg class="ico" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p[$name] . '</svg>';
}

/* ---------- Proses form ---------- */
$errors = ['profil' => [], 'password' => []];
$old = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.');
    }

    if (($_POST['aksi'] ?? '') === 'profil') {
        $nama    = trim($_POST['nama'] ?? '');
        $jurusan = trim($_POST['jurusan'] ?? '');
        $nim     = trim($_POST['nim'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $hp      = trim($_POST['hp'] ?? '');
        $old     = array_merge($old, compact('nama', 'jurusan', 'nim', 'email', 'hp'));

        if ($nama === '' || mb_strlen($nama) < 2)          $errors['profil']['nama']    = 'Nama lengkap minimal 2 karakter.';
        if ($jurusan === '')                               $errors['profil']['jurusan'] = 'Jurusan wajib diisi.';
        if (!preg_match('/^[0-9]{5,20}$/', $nim))          $errors['profil']['nim']     = 'NIM hanya berisi angka (5–20 digit).';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors['profil']['email']   = 'Format email belum benar.';
        if (!preg_match('/^(\+62|0)[0-9]{8,13}$/', $hp))   $errors['profil']['hp']      = 'Nomor HP harus diawali 0 atau +62 (9–14 digit).';

        // Upload foto (opsional)
        $fotoBaru = $_SESSION['user']['foto'];
        if (!empty($_FILES['foto']['name'])) {
            $f = $_FILES['foto'];
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $mime = $f['error'] === UPLOAD_ERR_OK ? mime_content_type($f['tmp_name']) : '';
            if ($f['error'] !== UPLOAD_ERR_OK) {
                $errors['profil']['foto'] = 'Foto gagal diunggah. Coba lagi.';
            } elseif (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) || strpos($mime, 'image/') !== 0) {
                $errors['profil']['foto'] = 'Gunakan foto berformat JPG, PNG, atau WEBP.';
            } elseif ($f['size'] > 2 * 1024 * 1024) {
                $errors['profil']['foto'] = 'Ukuran foto maksimal 2 MB.';
            } else {
                $dir = __DIR__ . '/uploads';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $nama_file = 'foto_' . bin2hex(random_bytes(8)) . '.' . $ext;
                if (move_uploaded_file($f['tmp_name'], "$dir/$nama_file")) {
                    $fotoBaru = 'uploads/' . $nama_file;
                } else {
                    $errors['profil']['foto'] = 'Folder uploads tidak bisa ditulis.';
                }
            }
        }

        if (!$errors['profil']) {
            $_SESSION['user'] = array_merge($_SESSION['user'], compact('nama', 'jurusan', 'nim', 'email', 'hp'), ['foto' => $fotoBaru]);
            $_SESSION['flash'] = ['ok', 'Perubahan profil tersimpan.'];
            header('Location: profil.php');
            exit;
        }
    }

    if (($_POST['aksi'] ?? '') === 'password') {
        $lama = $_POST['password_lama'] ?? '';
        $baru = $_POST['password_baru'] ?? '';
        $konf = $_POST['password_konfirmasi'] ?? '';

        if (!password_verify($lama, $_SESSION['user']['password'])) $errors['password']['lama'] = 'Password saat ini salah.';
        if (strlen($baru) < 8)                                      $errors['password']['baru'] = 'Password baru minimal 8 karakter.';
        elseif ($baru === $lama)                                    $errors['password']['baru'] = 'Password baru harus berbeda dari yang lama.';
        if ($konf !== $baru)                                        $errors['password']['konf'] = 'Konfirmasi tidak sama dengan password baru.';

        if (!$errors['password']) {
            $_SESSION['user']['password'] = password_hash($baru, PASSWORD_DEFAULT);
            $_SESSION['flash'] = ['ok', 'Password berhasil diubah.'];
            header('Location: profil.php');
            exit;
        }
        $_SESSION['flash_now'] = ['err', 'Password belum bisa diubah. Periksa kolom yang ditandai.'];
    }
    if ($errors['profil'] && !isset($_SESSION['flash_now'])) {
        $_SESSION['flash_now'] = ['err', 'Perubahan belum tersimpan. Periksa kolom yang ditandai.'];
    }
}

$flash = $_SESSION['flash'] ?? ($_SESSION['flash_now'] ?? null);
unset($_SESSION['flash'], $_SESSION['flash_now']);

$u = $old;
$foto = $_SESSION['user']['foto'];

// Variabel untuk layout (sama seperti halaman lain)
$page_title   = "Profil Saya";
$active_menu  = "profil"; // dashboard, unggah, catatan_saya, koleksi, tugas, profil
$user_name    = $_SESSION['user']['nama'];
$user_role    = $_SESSION['user']['role'];
$user_initial = mb_strtoupper(mb_substr($user_name, 0, 1));

function field($errs, $key) {
    return isset($errs[$key]) ? '<p class="err" id="err-' . $key . '">' . e($errs[$key]) . '</p>' : '';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title); ?> - CatatIn</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="profile.css">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
                <!-- Header Sidebar + Tombol Close (X) -->
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                        <a href="../dashboard_user/dashboard.php" class="nav-link <?= ($active_menu == 'dashboard') ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="../upload/upload.php" class="nav-link <?= ($active_menu == 'unggah') ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon> Unggah Catatan
                        </a>
                    </li>
                    <li>
                        <a href="../catatan saya/catatan_saya.php" class="nav-link <?= ($active_menu == 'catatan_saya') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon> Catatan Saya
                        </a>
                    </li>
                    <li>
                        <a href="../playlist/playlist.php" class="nav-link <?= ($active_menu == 'koleksi') ? 'active' : ''; ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon> Koleksi
                        </a>
                    </li>
                    <li>
                        <a href="../Pengingat/tugas.php" class="nav-link <?= ($active_menu == 'tugas') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon> Tugas
                        </a>
                    </li>
                    <li>
                        <a href="<?= basename(__FILE__); ?>" class="nav-link <?= ($active_menu == 'profil') ? 'active' : ''; ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon> Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="../logout.php" class="nav-link logout">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon> Keluar
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
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

            <!-- Area Konten Utama -->
            <main class="content-area">
                <div class="profil-page">

    <?php if ($flash): ?>
        <div class="toast <?= $flash[0] === 'ok' ? 'ok' : 'bad' ?>" role="status" id="toast">
            <?= icon($flash[0] === 'ok' ? 'check' : 'alert') ?><span><?= e($flash[1]) ?></span>
        </div>
    <?php endif; ?>

    <section class="stats" aria-label="Ringkasan">
        <article class="card stat">
            <div class="stat-ico"><?= icon('like', 26) ?></div>
            <div><b class="num" data-count="<?= (int)$stats['upvote'] ?>"><?= (int)$stats['upvote'] ?></b><p>Total upvote diterima</p></div>
        </article>
        <article class="card stat">
            <div class="stat-ico"><?= icon('share', 26) ?></div>
            <div><b class="num" data-count="<?= (int)$stats['catatan'] ?>"><?= (int)$stats['catatan'] ?></b><p>Catatan diunggah</p></div>
        </article>
        <article class="card stat">
            <div class="medal" aria-hidden="true">
                <svg width="38" height="46" viewBox="0 0 38 46"><path d="M8 0h9l4 14h-9z" fill="#3B5BDB"/><path d="M30 0h-9l-4 14h9z" fill="#E8590C"/><circle cx="19" cy="30" r="14" fill="#F5B301"/><circle cx="19" cy="30" r="10" fill="#FFD43B" stroke="#E09F00" stroke-width="1.5"/><path d="m19 24 1.9 4 4.3.5-3.2 3 .9 4.3-3.9-2.2-3.9 2.2.9-4.3-3.2-3 4.3-.5z" fill="#F08C00"/></svg>
            </div>
            <div><b class="badge-name"><?= e($stats['badge']) ?></b><p>Badge reputasi</p></div>
        </article>
    </section>

    <div class="grid">
        <!-- ===== Informasi Akun ===== -->
        <section class="card panel" aria-labelledby="h-akun">
            <h2 id="h-akun">Informasi Akun</h2>
            <form method="post" enctype="multipart/form-data" id="formProfil" novalidate>
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <input type="hidden" name="aksi" value="profil">

                <div class="identity">
                    <div class="photo" id="photoBox">
                        <?php if ($foto): ?>
                            <img src="<?= e($foto) ?>" alt="Foto profil" id="photoImg">
                        <?php else: ?>
                            <span id="photoDefault">
                                <svg width="54" height="54" viewBox="0 0 64 64" aria-hidden="true">
                                    <circle cx="32" cy="32" r="32" fill="#111"/>
                                    <circle cx="32" cy="25" r="10" fill="#fff"/>
                                    <path d="M8 58c3-11 12-17 24-17s21 6 24 17a32 32 0 0 1-48 0z" fill="#fff"/>
                                </svg>
                            </span>
                            <img src="" alt="Foto profil" id="photoImg" hidden>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong class="id-name" id="liveName"><?= e($u['nama']) ?></strong>
                        <small id="liveJurusan"><?= e($u['jurusan']) ?></small>
                    </div>
                </div>

                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp" hidden>
                <button type="button" class="btn btn-outline" id="btnFoto"><?= icon('camera', 18) ?>Ganti Foto</button>
                <?= field($errors['profil'], 'foto') ?>

                <div class="field">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= e($u['nama']) ?>" autocomplete="name" required<?= isset($errors['profil']['nama']) ? ' class="invalid"' : '' ?>>
                    <?= field($errors['profil'], 'nama') ?>
                </div>
                <div class="field">
                    <label for="jurusan">Jurusan</label>
                    <input type="text" id="jurusan" name="jurusan" value="<?= e($u['jurusan']) ?>" required<?= isset($errors['profil']['jurusan']) ? ' class="invalid"' : '' ?>>
                    <?= field($errors['profil'], 'jurusan') ?>
                </div>
                <div class="field">
                    <label for="nim">NIM/Nomor Induk</label>
                    <input type="text" id="nim" name="nim" value="<?= e($u['nim']) ?>" inputmode="numeric" maxlength="20" required<?= isset($errors['profil']['nim']) ? ' class="invalid"' : '' ?>>
                    <?= field($errors['profil'], 'nim') ?>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($u['email']) ?>" autocomplete="email" required<?= isset($errors['profil']['email']) ? ' class="invalid"' : '' ?>>
                    <?= field($errors['profil'], 'email') ?>
                </div>
                <div class="field">
                    <label for="hp">Nomor HP</label>
                    <input type="tel" id="hp" name="hp" value="<?= e($u['hp']) ?>" inputmode="tel" autocomplete="tel" required<?= isset($errors['profil']['hp']) ? ' class="invalid"' : '' ?>>
                    <?= field($errors['profil'], 'hp') ?>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary" id="btnSimpan"><?= icon('save', 20) ?>Simpan Perubahan</button>
                    <span class="dirty-note" id="dirtyNote" hidden>Ada perubahan yang belum disimpan</span>
                </div>
            </form>
        </section>

        <!-- ===== Keamanan & Kata Sandi ===== -->
        <section class="card panel panel-security" aria-labelledby="h-pass">
            <h2 id="h-pass">Keamanan &amp; Kata Sandi</h2>
            <p class="hint">Gunakan password yang kuat untuk menjaga akunmu</p>
            <form method="post" id="formPass" novalidate>
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <input type="hidden" name="aksi" value="password">

                <div class="field">
                    <label for="pl">Password Saat Ini</label>
                    <div class="pw">
                        <input type="password" id="pl" name="password_lama" placeholder="Masukkan password saat ini" autocomplete="current-password" required<?= isset($errors['password']['lama']) ? ' class="invalid"' : '' ?>>
                        <button type="button" class="eye" data-target="pl" aria-label="Tampilkan password"><?= icon('eye-off', 20) ?></button>
                    </div>
                    <?= field($errors['password'], 'lama') ?>
                </div>
                <div class="field">
                    <label for="pb">Password Baru</label>
                    <div class="pw">
                        <input type="password" id="pb" name="password_baru" placeholder="Masukkan password baru" autocomplete="new-password" minlength="8" required<?= isset($errors['password']['baru']) ? ' class="invalid"' : '' ?>>
                        <button type="button" class="eye" data-target="pb" aria-label="Tampilkan password"><?= icon('eye-off', 20) ?></button>
                    </div>
                    <div class="meter" id="meter" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
                    <small class="meter-label" id="meterLabel"></small>
                    <?= field($errors['password'], 'baru') ?>
                </div>
                <div class="field">
                    <label for="pk">Konfirmasi Password Baru</label>
                    <div class="pw">
                        <input type="password" id="pk" name="password_konfirmasi" placeholder="Konfirmasi password baru" autocomplete="new-password" required<?= isset($errors['password']['konf']) ? ' class="invalid"' : '' ?>>
                        <button type="button" class="eye" data-target="pk" aria-label="Tampilkan password"><?= icon('eye-off', 20) ?></button>
                    </div>
                    <?= field($errors['password'], 'konf') ?>
                </div>

                <button type="submit" class="btn btn-primary" id="btnPassword"><?= icon('lock', 20) ?>Ubah Password</button>
            </form>
        </section>
    </div>
</div>
            </main>
        </div>
    </div>

    <script src="profile.js"></script>
</body>

</html>