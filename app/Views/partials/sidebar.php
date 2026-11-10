<?php
// Nilai default
$is_admin = $is_admin ?? false;
$active_menu = $active_menu ?? 'dashboard';
?>

<?php if ($is_admin): ?>

    <!-- SIDEBAR ADMIN -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">
            <div class="brand-logo">
                <svg width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>CatatIn</span>
            </div>

            <button type="button"
                class="sidebar-close-btn"
                id="sidebar-close-btn">
                <iconify-icon icon="lucide:x"></iconify-icon>
            </button>
        </div>

        <nav class="sidebar-nav">

            <a href="<?= BASE_URL ?>/?page=admin-dashboard"
                class="nav-item <?= $active_menu === 'dashboard' ? 'active' : ''; ?>">
                Dashboard
            </a>

            <a href="<?= BASE_URL ?>/index.php?page=kelola-catatan"
                class="nav-item <?= $active_menu === 'kelola_catatan' ? 'active' : '' ?>">
                Kelola Catatan
            </a>

            <a href="<?= BASE_URL ?>/index.php?page=kelola-pengguna"
                class="nav-item <?= $active_menu === 'kelola_pengguna' ? 'active' : '' ?>">
                Kelola Pengguna
            </a>

            <a href="<?= BASE_URL ?>/index.php?page=master-data"
                class="nav-item <?= $active_menu === 'master_data' ? 'active' : '' ?>">
                Master Data
            </a>

            <a href="<?= BASE_URL ?>/index.php?page=laporan-admin"
                class="nav-item <?= $active_menu === 'laporan' ? 'active' : '' ?>">
                Kelola Laporan
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=profil-admin"
                class="nav-item <?= $active_menu === 'profil' ? 'active' : '' ?>">
                Profil
            </a>

            <a href="<?= BASE_URL ?>/?page=logout"
                class="nav-item btn-logout"
                onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                Keluar
            </a>

        </nav>
    </aside>

<?php else: ?>

    <!-- SIDEBAR USER -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">

        <div>
            <div class="sidebar-header">

                <div class="brand">
                    <svg width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>CatatIn</span>
                </div>

                <button type="button"
                    class="close-sidebar-btn"
                    id="closeSidebar"
                    aria-label="Tutup Menu">
                    <iconify-icon icon="lucide:x"></iconify-icon>
                </button>

            </div>

            <ul class="nav-list">

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                        class="nav-link <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <iconify-icon icon="akar-icons:dashboard"></iconify-icon>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=unggah"
                        class="nav-link <?= $active_menu === 'unggah' ? 'active' : '' ?>">
                        <iconify-icon icon="reicon:document-upload"></iconify-icon>
                        Unggah Catatan
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=catatan"
                        class="nav-link <?= $active_menu === 'catatan_saya' ? 'active' : '' ?>">
                        <iconify-icon icon="hugeicons:note"></iconify-icon>
                        Catatan Saya
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=koleksi"
                        class="nav-link <?= $active_menu === 'koleksi' ? 'active' : '' ?>">
                        <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon>
                        Koleksi
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=tugas"
                        class="nav-link <?= $active_menu === 'tugas' ? 'active' : '' ?>">
                        <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon>
                        Tugas
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?page=profil"
                        class="nav-link <?= $active_menu === 'profil' ? 'active' : '' ?>">
                        <iconify-icon icon="iconamoon:profile-circle"></iconify-icon>
                        Profil
                    </a>
                </li>

            </ul>
        </div>

        <div>
            <a href="<?= BASE_URL ?>/?page=logout"
                class="nav-link logout"
                onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon>
                Keluar
            </a>
        </div>

    </aside>

<?php endif; ?>