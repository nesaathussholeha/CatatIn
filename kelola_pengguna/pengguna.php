<?php
session_start();

if (!isset($_SESSION['pengguna_data'])) {
    $_SESSION['pengguna_data'] = [
        [
            'id' => 1,
            'nama' => 'Sarah Faradila',
            'nim' => '2551506...003',
            'jurusan' => 'Informatika',
            'status' => 'Aktif'
        ],
        [
            'id' => 2,
            'nama' => 'Nazma Fairuz M.',
            'nim' => '2551506...001',
            'jurusan' => 'Sistem Informasi',
            'status' => 'Aktif'
        ],
        [
            'id' => 3,
            'nama' => 'Bagas Wicaksono',
            'nim' => '2551506...004',
            'jurusan' => 'Teknik Informatika',
            'status' => 'Aktif'
        ]
    ];
}


/* ==============================
   CRUD
================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';


    /* TAMBAH */

    if ($action === 'add') {

        $ids = array_column($_SESSION['pengguna_data'], 'id');

        $newId = empty($ids) ? 1 : max($ids) + 1;

        $_SESSION['pengguna_data'][] = [
            'id' => $newId,
            'nama' => trim($_POST['nama']),
            'nim' => trim($_POST['nim']),
            'jurusan' => trim($_POST['jurusan']),
            'status' => 'Aktif'
        ];
    }


    /* EDIT */

    if ($action === 'edit') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['pengguna_data'] as $key => $user) {

            if ($user['id'] === $id) {

                $_SESSION['pengguna_data'][$key]['nama'] =
                    trim($_POST['nama']);

                $_SESSION['pengguna_data'][$key]['nim'] =
                    trim($_POST['nim']);

                $_SESSION['pengguna_data'][$key]['jurusan'] =
                    trim($_POST['jurusan']);

                break;
            }
        }
    }


    /* STATUS */

    if ($action === 'status') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['pengguna_data'] as $key => $user) {

            if ($user['id'] === $id) {

                $_SESSION['pengguna_data'][$key]['status'] =
                    $_POST['status'];

                break;
            }
        }
    }


    /* HAPUS */

    if ($action === 'delete') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['pengguna_data'] as $key => $user) {

            if ($user['id'] === $id) {
                unset($_SESSION['pengguna_data'][$key]);
                break;
            }
        }

        $_SESSION['pengguna_data'] =
            array_values($_SESSION['pengguna_data']);
    }


    header('Location: pengguna.php');
    exit;
}


function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

$pengguna = $_SESSION['pengguna_data'];

$namaAdmin = $_SESSION['user_name'] ?? 'Administrator';
$roleAdmin = $_SESSION['role'] ?? 'Admin';
$avatar = strtoupper(substr($namaAdmin, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CatatIn - Kelola Pengguna</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="pengguna.css">

</head>

<body>

    <div class="admin-wrapper">

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

                <button class="sidebar-close-btn" id="sidebar-close-btn">
                    ×
                </button>

            </div>


            <nav class="sidebar-nav">

                <a href="../dashboard_admin/dashboard.php" class="nav-item">
                    Dashboard
                </a>

                <a href="../kelola_catatan/catatan.php" class="nav-item">
                    Kelola Catatan
                </a>

                <a href="../kelola_pengguna/pengguna.php" class="nav-item active">
                    Kelola Pengguna
                </a>

                <a href="../master_data/index.php" class="nav-item">
                    Master Data
                </a>

                <a href="../report/report_admin.php" class="nav-item">
                    Kelola Laporan
                </a>

                <a href="../logout.php" class="nav-item btn-logout"
                    onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    Keluar
                </a>

            </nav>

        </aside>


        <main class="main-content">

            <header class="top-header">

                <div class="header-left">

                    <button class="hamburger-btn"
                        id="hamburger-btn">
                        ☰
                    </button>

                    <h1>Kelola pengguna</h1>

                </div>


                <div class="user-profile">

                    <div class="user-info">

                        <span class="user-name">
                            <?= e($namaAdmin); ?>
                        </span>

                        <span class="user-role">
                            <?= e(ucfirst($roleAdmin)); ?>
                        </span>

                    </div>

                    <div class="user-avatar">
                        <?= e($avatar); ?>
                    </div>

                </div>

            </header>


            <section class="page-card">

                <div class="page-card-header">

                    <h2>Daftar pengguna</h2>

                    <div class="toolbar">

                        <input type="text"
                            id="search-input"
                            placeholder="Cari nama atau NIM...">

                        <button class="add-button"
                            id="open-add-modal">

                            + Tambah pengguna

                        </button>

                    </div>

                </div>


                <div class="table-container">

                    <table id="user-table">

                        <thead>

                            <tr>

                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($pengguna as $user): ?>

                                <tr>

                                    <td>
                                        <?= e($user['nama']); ?>
                                    </td>

                                    <td>
                                        <?= e($user['nim']); ?>
                                    </td>

                                    <td>
                                        <?= e($user['jurusan']); ?>
                                    </td>

                                    <td>

                                        <span class="status-badge
                                <?= $user['status'] === 'Aktif'
                                    ? 'status-active'
                                    : 'status-blocked'; ?>">

                                            <?= e($user['status']); ?>

                                        </span>

                                    </td>

                                    <td>

                                        <div class="action-wrap">

                                            <button class="action-button dropdown-trigger">

                                                Aksi <span>▼</span>

                                            </button>

                                            <div class="action-menu">

                                                <button class="menu-item view-user"
                                                    type="button"
                                                    data-name="<?= e($user['nama']); ?>"
                                                    data-nim="<?= e($user['nim']); ?>"
                                                    data-major="<?= e($user['jurusan']); ?>"
                                                    data-status="<?= e($user['status']); ?>">

                                                    Lihat

                                                </button>


                                                <button class="menu-item edit-user"
                                                    type="button"
                                                    data-id="<?= $user['id']; ?>"
                                                    data-name="<?= e($user['nama']); ?>"
                                                    data-nim="<?= e($user['nim']); ?>"
                                                    data-major="<?= e($user['jurusan']); ?>">

                                                    Edit

                                                </button>


                                                <form method="POST">

                                                    <input type="hidden"
                                                        name="action"
                                                        value="status">

                                                    <input type="hidden"
                                                        name="id"
                                                        value="<?= $user['id']; ?>">

                                                    <input type="hidden"
                                                        name="status"
                                                        value="<?= $user['status'] === 'Aktif'
                                                                    ? 'Diblokir'
                                                                    : 'Aktif'; ?>">

                                                    <button class="menu-item"
                                                        type="submit">

                                                        <?= $user['status'] === 'Aktif'
                                                            ? 'Blokir'
                                                            : 'Buka blokir'; ?>

                                                    </button>

                                                </form>


                                                <form method="POST"
                                                    onsubmit="return confirm('Hapus pengguna ini?');">

                                                    <input type="hidden"
                                                        name="action"
                                                        value="delete">

                                                    <input type="hidden"
                                                        name="id"
                                                        value="<?= $user['id']; ?>">

                                                    <button class="menu-item delete-item">
                                                        Hapus
                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>


    <!-- MODAL TAMBAH -->

    <div class="modal-overlay" id="add-modal">

        <div class="modal">

            <button class="modal-close"
                data-close="add-modal">
                ×
            </button>

            <h2>Tambah pengguna</h2>

            <form method="POST">

                <input type="hidden"
                    name="action"
                    value="add">

                <label>Nama</label>

                <input type="text"
                    name="nama"
                    required>

                <label>NIM</label>

                <input type="text"
                    name="nim"
                    required>

                <label>Jurusan</label>

                <input type="text"
                    name="jurusan"
                    required>

                <button class="save-button">
                    Tambahkan
                </button>

            </form>

        </div>

    </div>


    <!-- MODAL EDIT -->

    <div class="modal-overlay" id="edit-modal">

        <div class="modal">

            <button class="modal-close"
                data-close="edit-modal">
                ×
            </button>

            <h2>Edit pengguna</h2>

            <form method="POST">

                <input type="hidden"
                    name="action"
                    value="edit">

                <input type="hidden"
                    name="id"
                    id="edit-id">

                <label>Nama</label>

                <input type="text"
                    name="nama"
                    id="edit-name"
                    required>

                <label>NIM</label>

                <input type="text"
                    name="nim"
                    id="edit-nim"
                    required>

                <label>Jurusan</label>

                <input type="text"
                    name="jurusan"
                    id="edit-major"
                    required>

                <button class="save-button">
                    Simpan perubahan
                </button>

            </form>

        </div>

    </div>


    <!-- MODAL DETAIL -->

    <div class="modal-overlay"
        id="view-modal">

        <div class="modal">

            <button class="modal-close"
                data-close="view-modal">
                ×
            </button>

            <h2>Detail pengguna</h2>

            <div class="detail-list">

                <div>
                    <span>Nama</span>
                    <strong id="view-name"></strong>
                </div>

                <div>
                    <span>NIM</span>
                    <strong id="view-nim"></strong>
                </div>

                <div>
                    <span>Jurusan</span>
                    <strong id="view-major"></strong>
                </div>

                <div>
                    <span>Status</span>
                    <strong id="view-status"></strong>
                </div>

            </div>

        </div>

    </div>


    <script src="pengguna.js"></script>

</body>

</html>