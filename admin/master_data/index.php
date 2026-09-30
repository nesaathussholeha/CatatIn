<?php
session_start();

// Proteksi halaman: Wajib login dan harus role 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Inisialisasi Data Dummy Jurusan
if (!isset($_SESSION['data_jurusan'])) {
    $_SESSION['data_jurusan'] = [
        ['kode' => 'TIF', 'nama' => 'Informatika', 'status' => 'Aktif'],
        ['kode' => 'SIF', 'nama' => 'Sistem Informasi', 'status' => 'Aktif'],
        ['kode' => 'TE',  'nama' => 'Elektro', 'status' => 'Aktif']
    ];
}

// Inisialisasi Data Dummy Mata Kuliah
if (!isset($_SESSION['data_matkul'])) {
    $_SESSION['data_matkul'] = [
        ['kode' => 'MK01', 'nama' => 'Pemrograman Web', 'status' => 'Aktif'],
        ['kode' => 'MK02', 'nama' => 'Basis Data', 'status' => 'Aktif'],
        ['kode' => 'MK03', 'nama' => 'Algoritma & Struktur Data', 'status' => 'Aktif']
    ];
}

// Inisialisasi Data Dummy Kategori Dokumen
if (!isset($_SESSION['data_kategori'])) {
    $_SESSION['data_kategori'] = [
        ['kode' => 'KAT01', 'nama' => 'Rangkuman / Catatan', 'status' => 'Aktif'],
        ['kode' => 'KAT02', 'nama' => 'Modul Praktikum', 'status' => 'Aktif'],
        ['kode' => 'KAT03', 'nama' => 'Latihan / Bank Soal', 'status' => 'Aktif']
    ];
}

// Logika Tambah Data Baru (CREATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $tab_active = $_POST['tab_type'] ?? 'jurusan';
    $nama_baru  = trim($_POST['nama_baru'] ?? '');

    if (!empty($nama_baru)) {
        if ($tab_active === 'jurusan') {
            $words = explode(" ", $nama_baru);
            $kode_baru = strtoupper(substr($words[0], 0, 3));
            $_SESSION['data_jurusan'][] = ['kode' => $kode_baru, 'nama' => $nama_baru, 'status' => 'Aktif'];
        } elseif ($tab_active === 'matkul') {
            $kode_baru = 'MK' . str_pad(count($_SESSION['data_matkul']) + 1, 2, '0', STR_PAD_LEFT);
            $_SESSION['data_matkul'][] = ['kode' => $kode_baru, 'nama' => $nama_baru, 'status' => 'Aktif'];
        } elseif ($tab_active === 'kategori') {
            $kode_baru = 'KAT' . str_pad(count($_SESSION['data_kategori']) + 1, 2, '0', STR_PAD_LEFT);
            $_SESSION['data_kategori'][] = ['kode' => $kode_baru, 'nama' => $nama_baru, 'status' => 'Aktif'];
        }
    }
    header("Location: index.php?tab=" . $tab_active);
    exit();
}

// Logika Toggle Status / Nonaktifkan (DELETE / SOFT DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
    $tab_target   = $_GET['tab'] ?? 'jurusan';
    $index_target = (int)$_GET['id'];

    if ($tab_target === 'jurusan' && isset($_SESSION['data_jurusan'][$index_target])) {
        $st = $_SESSION['data_jurusan'][$index_target]['status'];
        $_SESSION['data_jurusan'][$index_target]['status'] = ($st === 'Aktif') ? 'Nonaktif' : 'Aktif';
    } elseif ($tab_target === 'matkul' && isset($_SESSION['data_matkul'][$index_target])) {
        $st = $_SESSION['data_matkul'][$index_target]['status'];
        $_SESSION['data_matkul'][$index_target]['status'] = ($st === 'Aktif') ? 'Nonaktif' : 'Aktif';
    } elseif ($tab_target === 'kategori' && isset($_SESSION['data_kategori'][$index_target])) {
        $st = $_SESSION['data_kategori'][$index_target]['status'];
        $_SESSION['data_kategori'][$index_target]['status'] = ($st === 'Aktif') ? 'Nonaktif' : 'Aktif';
    }

    header("Location: index.php?tab=" . $tab_target);
    exit();
}

// Tentukan Tab Aktif
$current_tab = $_GET['tab'] ?? 'jurusan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatatIn - Master Data Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="admin-wrapper">
        <!-- Sidebar Overlay (untuk menutup menu saat diklik di luar area pada mobile) -->
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
                <a href="../../dashboard_admin.php" class="nav-item">Dashboard</a>
                <a href="#" class="nav-item">Kelola Catatan</a>
                <a href="#" class="nav-item">Kelola Pengguna</a>
                <a href="index.php" class="nav-item active">Master Data</a>
                <a href="#" class="nav-item">Kelola Laporan</a>
                <a href="../../logout.php" class="nav-item btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">Keluar</a>
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

            <!-- Tab Switcher Navigation -->
            <div class="tab-switcher">
                <button type="button" class="tab-button <?php echo ($current_tab === 'jurusan') ? 'active' : ''; ?>" id="tab-jurusan-btn">Jurusan</button>
                <button type="button" class="tab-button <?php echo ($current_tab === 'matkul') ? 'active' : ''; ?>" id="tab-matkul-btn">Mata Kuliah</button>
                <button type="button" class="tab-button <?php echo ($current_tab === 'kategori') ? 'active' : ''; ?>" id="tab-kategori-btn">Kategori Dokumen</button>
            </div>

            <!-- ACTION BAR: Search di KIRI, Form Tambah di KANAN -->
            <div class="action-bar">
                <div class="search-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" placeholder="Cari data..." autocomplete="off">
                </div>

                <form action="index.php" method="POST" class="add-form" id="main-add-form">
                    <input type="hidden" name="action" value="tambah">
                    <input type="hidden" name="tab_type" id="tab_type_input" value="<?php echo htmlspecialchars($current_tab); ?>">
                    <input type="text" name="nama_baru" id="input-nama-baru" placeholder="Ketik nama baru & tekan Enter..." required autocomplete="off">
                    <button type="submit" class="btn-add">+ Tambah</button>
                </form>
            </div>

            <!-- TAB 1: JURUSAN -->
            <div class="tab-content <?php echo ($current_tab === 'jurusan') ? 'active' : ''; ?>" id="tab-jurusan-content">
                <div class="table-responsive">
                    <table class="data-table" id="table-jurusan">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Jurusan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['data_jurusan'] as $index => $item): ?>
                                <tr>
                                    <td class="font-bold cell-kode"><?php echo htmlspecialchars($item['kode']); ?></td>
                                    <td class="cell-nama"><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($item['status'] === 'Aktif') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo htmlspecialchars($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center actions-cell">
                                        <button type="button" class="btn-action btn-edit" onclick="editData('jurusan', '<?php echo addslashes($item['nama']); ?>')">Edit</button>
                                        <a href="index.php?action=toggle_status&tab=jurusan&id=<?php echo $index; ?>" class="btn-action btn-toggle">
                                            <?php echo ($item['status'] === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: MATA KULIAH -->
            <div class="tab-content <?php echo ($current_tab === 'matkul') ? 'active' : ''; ?>" id="tab-matkul-content">
                <div class="table-responsive">
                    <table class="data-table" id="table-matkul">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['data_matkul'] as $index => $item): ?>
                                <tr>
                                    <td class="font-bold cell-kode"><?php echo htmlspecialchars($item['kode']); ?></td>
                                    <td class="cell-nama"><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($item['status'] === 'Aktif') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo htmlspecialchars($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center actions-cell">
                                        <button type="button" class="btn-action btn-edit" onclick="editData('matkul', '<?php echo addslashes($item['nama']); ?>')">Edit</button>
                                        <a href="index.php?action=toggle_status&tab=matkul&id=<?php echo $index; ?>" class="btn-action btn-toggle">
                                            <?php echo ($item['status'] === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: KATEGORI DOKUMEN -->
            <div class="tab-content <?php echo ($current_tab === 'kategori') ? 'active' : ''; ?>" id="tab-kategori-content">
                <div class="table-responsive">
                    <table class="data-table" id="table-kategori">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kategori</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['data_kategori'] as $index => $item): ?>
                                <tr>
                                    <td class="font-bold cell-kode"><?php echo htmlspecialchars($item['kode']); ?></td>
                                    <td class="cell-nama"><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($item['status'] === 'Aktif') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo htmlspecialchars($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center actions-cell">
                                        <button type="button" class="btn-action btn-edit" onclick="editData('kategori', '<?php echo addslashes($item['nama']); ?>')">Edit</button>
                                        <a href="index.php?action=toggle_status&tab=kategori&id=<?php echo $index; ?>" class="btn-action btn-toggle">
                                            <?php echo ($item['status'] === 'Aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>