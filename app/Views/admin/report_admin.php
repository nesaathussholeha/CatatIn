<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatatIn - <?= htmlspecialchars($pageTitle); ?></title>
    
    <!-- Google Fonts & Iconify -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <link rel="stylesheet" href="public/css/report_admin.css">
</head>
<body>

    <div class="app-container">
        <!-- SIDEBAR OVERLAY -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- SIDEBAR ADMIN -->
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

                    <button type="button" class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="index.php?page=dashboard_admin" class="nav-link <?= $activeMenu === 'dashboard_admin' ? 'active' : '' ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=kelola_catatan" class="nav-link <?= $activeMenu === 'kelola_catatan' ? 'active' : '' ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon>
                            Kelola Catatan
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=kelola_pengguna" class="nav-link <?= $activeMenu === 'kelola_pengguna' ? 'active' : '' ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon>
                            Kelola Pengguna
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=master_data" class="nav-link <?= $activeMenu === 'master_data' ? 'active' : '' ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon>
                            Master Data
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=report_admin" class="nav-link <?= $activeMenu === 'laporan' ? 'active' : '' ?>">
                            <iconify-icon icon="lucide:flag"></iconify-icon>
                            Kelola Laporan
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="index.php?page=logout" class="nav-link logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon>
                    Keluar
                </a>
            </div>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka Menu">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title">Laporan Masuk</h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($userName); ?></span>
                        <span class="user-role"><?= htmlspecialchars($userRole); ?></span>
                    </div>
                    <div class="avatar" title="Profil Admin">
                        <?= htmlspecialchars($userInit); ?>
                    </div>
                </div>
            </header>

            <!-- KONTEN UTAMA LAPORAN -->
            <main class="content-area">
                <div class="filter-tabs" id="filter-tabs" role="tablist">
                    <button type="button" class="tab-btn active" data-filter="semua" role="tab">Semua</button>
                    <button type="button" class="tab-btn" data-filter="menunggu" role="tab">Menunggu</button>
                    <button type="button" class="tab-btn" data-filter="diproses" role="tab">Diproses</button>
                    <button type="button" class="tab-btn" data-filter="selesai" role="tab">Selesai</button>
                </div>

                <section class="table-card">
                    <div class="table-scroll">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th class="col-catatan">Catatan</th>
                                    <th class="col-center">Pelapor</th>
                                    <th class="col-center">Alasan</th>
                                    <th class="col-center">Status</th>
                                    <th class="col-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="report-body"></tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Dropdown Status -->
    <div class="status-menu" id="status-menu" role="menu" hidden>
        <button type="button" class="status-option opt-menunggu" data-status="menunggu" role="menuitem">Menunggu</button>
        <button type="button" class="status-option opt-diproses" data-status="diproses" role="menuitem">Diproses</button>
        <button type="button" class="status-option opt-selesai" data-status="selesai" role="menuitem">Selesai</button>
    </div>

    <!-- Modal Detail -->
    <div class="modal-backdrop" id="detail-modal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="detail-title">
            <div class="modal-header">
                <h2 id="detail-title">Detail Laporan</h2>
                <button type="button" class="modal-close" data-close aria-label="Tutup">
                    <iconify-icon icon="lucide:x"></iconify-icon>
                </button>
            </div>
            <div class="modal-body" id="detail-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-close>Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal-backdrop" id="delete-modal" aria-hidden="true">
        <div class="modal modal-sm" role="alertdialog" aria-modal="true" aria-labelledby="delete-title">
            <div class="modal-header">
                <h2 id="delete-title">Hapus Laporan?</h2>
            </div>
            <div class="modal-body">
                <p class="confirm-text" id="delete-text"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-close>Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast" role="status" aria-live="polite"></div>

    <script>
        window.LAPORAN_DATA = <?= json_encode($laporan, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    </script>
    <script src="public/js/report_admin.js"></script>
</body>
</html>