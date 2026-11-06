<?php
// Nilai default jika halaman lupa mendefinisikannya
$page_title  = isset($page_title) ? $page_title : "CatatIn";
$active_menu = isset($active_menu) ? $active_menu : "dashboard";
$user_name   = isset($user_name) ? $user_name : "Username";
$user_role   = isset($user_role) ? $user_role : "Role";
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title; ?> - CatatIn</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="../layout_user/style.css">
    <link rel="stylesheet" href="../profile_penyusun/style.css">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
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
                        <a href="../profile/profile.php" class="nav-link <?= ($active_menu == 'profil') ? 'active' : ''; ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon> Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="logout.php" class="nav-link logout">
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
                    <h1 class="page-title"><?= $page_title; ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= $user_name; ?></span>
                        <span class="user-role"><?= $user_role; ?></span>
                    </div>
                    <div class="avatar">
                        <?= $user_initial; ?>
                    </div>
                </div>
            </header>

            <!-- Area Konten Utama Mulai Di sini -->
            <!-- Di dalam file layout_user/index.php -->
            <main class="content-area">
                <div>
                    <?php
                    // Cetak konten dari halaman yang memanggilnya
                    if (isset($content)) {
                        echo $content;
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script src="../layout_user/script.js"></script>
    <script src="../profile_penyusun/script.js"></script>
</body>

</html>