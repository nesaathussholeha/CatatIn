<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatatIn - Master Data Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../layout_admin/style.css">
</head>
<body>

    <div class="admin-wrapper">
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>CatatIn</span>
                </div>
                
                <!-- Tombol Close Sidebar untuk Mobile -->
                <button type="button" class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Tutup Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <a href="../admin/admin.php" class="nav-item">Dashboard</a>
                <a href="../" class="nav-item">Kelola Catatan</a>
                <a href="#" class="nav-item">Kelola Pengguna</a>
                <a href="index.php" class="nav-item active">Master Data</a>
                <a href="#" class="nav-item">Kelola Laporan</a>
                <a href="logout.php" class="nav-item btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">Keluar</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <!-- Tombol Hamburger Menu (Mobile Only) -->
                    <button type="button" class="hamburger-btn" id="hamburger-btn" aria-label="Buka Menu">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title">Master data</h1>
                </div>

                <!-- Profil User: Nama, Role, & Avatar -->
                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></span>
                        <span class="user-role"><?php echo ucfirst($_SESSION['role'] ?? 'Admin'); ?></span>
                    </div>
                    <div class="user-avatar" title="Profil Admin">
                        <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?>
                    </div>
                </div>
            </header>


            
                <!-- ISI KONTEN -->

        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>