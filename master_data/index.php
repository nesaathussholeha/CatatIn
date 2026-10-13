<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard_user/dashboard.php");
    exit();
}

$data_dir  = __DIR__ . "/data";
$data_file = $data_dir . "/master_data.json";

if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}

if (!file_exists($data_file)) {
    $initial_data = [
        "jurusan" => [
            ["id" => "j1", "kode" => "TIF", "nama" => "Informatika", "status" => true, "pengguna" => "42 Catatan"],
            ["id" => "j2", "kode" => "SI", "nama" => "Sistem Informasi", "status" => true, "pengguna" => "0 Catatan"],
            ["id" => "j3", "kode" => "TEKOM", "nama" => "Teknik Komputer", "status" => false, "pengguna" => "12 Catatan"]
        ],
        "matakuliah" => [
            ["id" => "m1", "kode" => "MK01", "nama" => "Pemrograman Web", "jurusan" => "Informatika", "status" => true, "pengguna" => "25 Catatan"],
            ["id" => "m2", "kode" => "MK02", "nama" => "Basis Data", "jurusan" => "Sistem Informasi", "status" => true, "pengguna" => "0 Catatan"]
        ],
        "kategori" => [
            ["id" => "k1", "kode" => "KAT01", "nama" => "Rangkuman Materi", "status" => true, "pengguna" => "50 Catatan"],
            ["id" => "k2", "kode" => "KAT02", "nama" => "Latihan Soal", "status" => false, "pengguna" => "0 Catatan"]
        ]
    ];
    file_put_contents($data_file, json_encode($initial_data, JSON_PRETTY_PRINT));
}

$master_data = json_decode(file_get_contents($data_file), true);

$active_tab = $_GET['tab'] ?? $_SESSION['active_tab'] ?? 'jurusan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $tab    = $_POST['tab'] ?? 'jurusan';

    if ($action === 'save') {
        $id     = $_POST['id'] ?? '';
        $kode   = trim($_POST['kode'] ?? '');
        $nama   = trim($_POST['nama'] ?? '');
        $extra  = trim($_POST['extra'] ?? '');

        if ($id === '') {
            $new_item = [
                "id"       => uniqid(),
                "kode"     => strtoupper($kode),
                "nama"     => $nama,
                "status"   => true,
                "pengguna" => "0 Catatan"
            ];
            if ($tab === 'matakuliah') {
                $new_item['jurusan'] = $extra;
            }
            $master_data[$tab][] = $new_item;
        } else {
            foreach ($master_data[$tab] as &$item) {
                if ($item['id'] === $id) {
                    $item['kode'] = strtoupper($kode);
                    $item['nama'] = $nama;
                    if ($tab === 'matakuliah') {
                        $item['jurusan'] = $extra;
                    }
                    break;
                }
            }
        }
    } elseif ($action === 'toggle_status') {
        $id = $_POST['id'] ?? '';
        foreach ($master_data[$tab] as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = !$item['status'];
                break;
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        foreach ($master_data[$tab] as $index => $item) {
            if ($item['id'] === $id) {
                $count = (int) preg_replace('/[^0-9]/', '', $item['pengguna'] ?? '0');
                if ($count === 0) {
                    array_splice($master_data[$tab], $index, 1);
                }
                break;
            }
        }
    }

    file_put_contents($data_file, json_encode($master_data, JSON_PRETTY_PRINT));
    header("Location: index.php?tab=" . $tab);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data - CatatIn</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>CatatIn</span>
                </div>
                <button class="sidebar-close-btn" id="sidebar-close">&times;</button>
            </div>

            <nav class="sidebar-nav">
                <a href="../dashboard_admin/dashboard.php" class="nav-item">Dashboard</a>
                <a href="../kelola_catatan/catatan.php" class="nav-item">Kelola Catatan</a>
                <a href="../kelola_pengguna/pengguna.php" class="nav-item">Kelola Pengguna</a>
                <a href="index.php" class="nav-item active">Master Data</a>
                <a href="../report/report_admin.php" class="nav-item">Kelola Laporan</a>
                <a href="../logout.php" class="nav-item btn-logout">Keluar</a>
            </nav>
        </aside>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="hamburger-btn" id="hamburger-btn">☰</button>
                    <h1 class="page-title">Master Data</h1>
                </div>
                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name">Administrator 1</span>
                        <span class="user-role">Admin</span>
                    </div>
                    <div class="user-avatar">A</div>
                </div>
            </header>

            <!-- Isi Halaman Utama -->
            <div class="content-body">
                <!-- Switcher Tab -->
                <div class="tab-switcher">
                    <button class="tab-btn <?php echo $active_tab === 'jurusan' ? 'active' : ''; ?>" data-tab="jurusan">Jurusan</button>
                    <button class="tab-btn <?php echo $active_tab === 'matakuliah' ? 'active' : ''; ?>" data-tab="matakuliah">Mata Kuliah</button>
                    <button class="tab-btn <?php echo $active_tab === 'kategori' ? 'active' : ''; ?>" data-tab="kategori">Kategori Dokumen</button>
                </div>

                <!-- Action Bar -->
                <div class="action-bar">
                    <div class="search-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="search-input" placeholder="Cari Data...">
                    </div>
                    <button type="button" class="btn-primary" id="btn-tambah">+ Tambah</button>
                </div>

                <!-- Kartu Tabel Data -->
                <div class="table-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr id="table-headers"></tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Form Pop-up -->
    <div class="modal-overlay" id="modal-form">
        <div class="modal">
            <button type="button" class="modal-close" id="btn-close-modal">&times;</button>
            <h2 id="modal-title">Tambah Data</h2>

            <form action="index.php" method="POST" id="form-master">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="tab" id="modal-tab" value="<?php echo $active_tab; ?>">
                <input type="hidden" name="id" id="modal-id" value="">

                <div class="form-body">
                    <div class="form-group">
                        <label for="input-kode" id="label-kode">Kode</label>
                        <input type="text" id="input-kode" name="kode" required placeholder="Masukkan kode">
                    </div>

                    <div class="form-group">
                        <label for="input-nama" id="label-nama">Nama Jurusan <span class="required">*</span></label>
                        <input type="text" id="input-nama" name="nama" required placeholder="Masukkan nama">
                    </div>

                    <div class="form-group hidden" id="group-extra">
                        <label for="input-extra">Jurusan</label>
                        <select id="input-extra" name="extra">
                            <?php foreach ($master_data['jurusan'] as $j): ?>
                                <option value="<?php echo htmlspecialchars($j['nama']); ?>"><?php echo htmlspecialchars($j['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="secondary-button" id="btn-batal">Batal</button>
                    <button type="submit" class="primary-button">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Hidden Aksi Nonaktif/Hapus -->
    <form id="action-form" action="index.php" method="POST" style="display:none;">
        <input type="hidden" name="action" id="action-type">
        <input type="hidden" name="tab" id="action-tab" value="<?php echo $active_tab; ?>">
        <input type="hidden" name="id" id="action-id">
    </form>

    <script>
        const initialMasterData = <?php echo json_encode($master_data); ?>;
        const initialActiveTab = "<?php echo $active_tab; ?>";
    </script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>