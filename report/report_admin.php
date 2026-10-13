<?php
session_start();

$userName = $_SESSION['user_name'] ?? 'Administrator 1';
$userRole = ucfirst($_SESSION['role'] ?? 'Admin');
$userInit = strtoupper(substr($userName, 0, 1));

$laporan = [
    [
        'id'       => 1,
        'catatan'  => 'Catatan UAS Basis Data',
        'pelapor'  => 'Nazma F',
        'alasan'   => 'File Rusak',
        'status'   => 'diproses',
        'tanggal'  => '2026-10-10',
        'deskripsi'=> 'File PDF tidak bisa dibuka dan menampilkan pesan error saat diunduh.'
    ],
    [
        'id'       => 2,
        'catatan'  => 'Rangkuman Statistika',
        'pelapor'  => 'Nazma F',
        'alasan'   => 'Salah Matkul',
        'status'   => 'menunggu',
        'tanggal'  => '2026-10-11',
        'deskripsi'=> 'Catatan ini seharusnya berada di mata kuliah Probabilitas, bukan Statistika.'
    ],
    [
        'id'       => 3,
        'catatan'  => 'Modul Jaringan Komputer',
        'pelapor'  => 'Nazma F',
        'alasan'   => 'Spam',
        'status'   => 'selesai',
        'tanggal'  => '2026-10-12',
        'deskripsi'=> 'Isi catatan berisi promosi yang tidak berkaitan dengan materi kuliah.'
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatatIn - Kelola Laporan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="report_admin.css">
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
                <a href="#" class="nav-item">Master Data</a>
                <a href="laporan.php" class="nav-item active">Kelola Laporan</a>
                <a href="#" class="nav-item">Profil</a>
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
                    <h1 class="page-title">Laporan Masuk</h1>
                </div>

                <!-- Profil User: Nama, Role, & Avatar -->
                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars($userRole); ?></span>
                    </div>
                    <div class="user-avatar" title="Profil Admin">
                        <?php echo htmlspecialchars($userInit); ?>
                    </div>
                </div>
            </header>

            <!-- ISI KONTEN -->

            <!-- Tab Filter Status -->
            <div class="filter-tabs" id="filter-tabs" role="tablist">
                <button type="button" class="tab-btn active" data-filter="semua" role="tab">Semua</button>
                <button type="button" class="tab-btn" data-filter="menunggu" role="tab">Menunggu</button>
                <button type="button" class="tab-btn" data-filter="diproses" role="tab">Diproses</button>
                <button type="button" class="tab-btn" data-filter="selesai" role="tab">Selesai</button>
            </div>

            <!-- Tabel Laporan -->
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

    <!-- Dropdown Status (satu instance, dipindah posisinya via JS) -->
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
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
        window.LAPORAN_DATA = <?php echo json_encode($laporan, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    </script>
    <script src="report_admin.js"></script>
</body>
</html>