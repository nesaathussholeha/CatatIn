<?php
session_start();

if (!isset($_SESSION['catatan_data'])) {
    $_SESSION['catatan_data'] = [
        [
            'id' => 1,
            'judul' => 'Ringkasan Struktur Data — Binary Tree',
            'penulis' => 'Sarah Faradila',
            'jurusan' => 'Informatika',
            'upvote' => 128,
            'status' => 'Terbit',
            'isi' => 'Catatan mengenai struktur data Binary Tree.',
            'alasan' => ''
        ],
        [
            'id' => 2,
            'judul' => 'Rangkuman Basis Data Relasional',
            'penulis' => 'Nazma Fairuz M.',
            'jurusan' => 'Sistem Informasi',
            'upvote' => 94,
            'status' => 'Terbit',
            'isi' => 'Rangkuman mengenai basis data relasional.',
            'alasan' => ''
        ],
        [
            'id' => 3,
            'judul' => 'Catatan Kalkulus II — Integral Lipat',
            'penulis' => 'Bagas Wicaksono',
            'jurusan' => 'Teknik Informatika',
            'upvote' => 61,
            'status' => 'Dilaporkan',
            'isi' => 'Pembahasan integral lipat.',
            'alasan' => 'Materi perlu diperiksa kembali.'
        ],
        [
            'id' => 4,
            'judul' => 'Ringkasan Jaringan Komputer — OSI Layer',
            'penulis' => 'Dewi Anjani',
            'jurusan' => 'Informatika',
            'upvote' => 45,
            'status' => 'Terbit',
            'isi' => 'Ringkasan mengenai OSI Layer.',
            'alasan' => ''
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

        $ids = array_column($_SESSION['catatan_data'], 'id');

        $newId = empty($ids) ? 1 : max($ids) + 1;

        $_SESSION['catatan_data'][] = [
            'id' => $newId,
            'judul' => trim($_POST['judul']),
            'penulis' => trim($_POST['penulis']),
            'jurusan' => trim($_POST['jurusan']),
            'upvote' => 0,
            'status' => 'Draft',
            'isi' => trim($_POST['isi']),
            'alasan' => ''
        ];
    }


    /* EDIT */

    if ($action === 'edit') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['catatan_data'] as $key => $note) {

            if ($note['id'] === $id) {

                $_SESSION['catatan_data'][$key]['judul'] =
                    trim($_POST['judul']);

                $_SESSION['catatan_data'][$key]['penulis'] =
                    trim($_POST['penulis']);

                $_SESSION['catatan_data'][$key]['jurusan'] =
                    trim($_POST['jurusan']);

                $_SESSION['catatan_data'][$key]['isi'] =
                    trim($_POST['isi']);

                break;
            }
        }
    }


    /* STATUS */

    if ($action === 'status') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['catatan_data'] as $key => $note) {

            if ($note['id'] === $id) {

                $_SESSION['catatan_data'][$key]['status'] =
                    $_POST['status'];

                if ($_POST['status'] === 'Terbit') {
                    $_SESSION['catatan_data'][$key]['alasan'] = '';
                }

                break;
            }
        }
    }


    /* HAPUS */

    if ($action === 'delete') {

        $id = (int) $_POST['id'];

        foreach ($_SESSION['catatan_data'] as $key => $note) {

            if ($note['id'] === $id) {
                unset($_SESSION['catatan_data'][$key]);
                break;
            }
        }

        $_SESSION['catatan_data'] =
            array_values($_SESSION['catatan_data']);
    }

    header('Location: catatan.php');
    exit;
}


function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

$catatan = $_SESSION['catatan_data'];

$namaAdmin = $_SESSION['user_name'] ?? 'Administrator';
$roleAdmin = $_SESSION['role'] ?? 'Admin';
$avatar = strtoupper(substr($namaAdmin, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CatatIn - Kelola Catatan</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="catatan.css">

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

                <a href="../kelola_catatan/catatan.php" class="nav-item active">
                    Kelola Catatan
                </a>

                <a href="../kelola_pengguna/pengguna.php" class="nav-item">
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

                    <button class="hamburger-btn" id="hamburger-btn">
                        ☰
                    </button>

                    <h1>Kelola catatan</h1>

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

                    <h2>Daftar catatan</h2>

                    <div class="toolbar">

                        <input type="text"
                            id="search-input"
                            placeholder="Cari judul atau penulis...">

                        <button class="add-button"
                            id="open-add-modal">

                            + Tambah catatan

                        </button>

                    </div>

                </div>


                <div class="table-container">

                    <table id="catatan-table">

                        <thead>

                            <tr>

                                <th>Judul catatan</th>
                                <th>Penulis</th>
                                <th>Jurusan</th>
                                <th>Upvote</th>
                                <th>Status</th>
                                <th>Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($catatan as $note): ?>

                                <tr>

                                    <td class="title-cell">
                                        <?= e($note['judul']); ?>
                                    </td>

                                    <td>
                                        <?= e($note['penulis']); ?>
                                    </td>

                                    <td>
                                        <?= e($note['jurusan']); ?>
                                    </td>

                                    <td>
                                        <?= e($note['upvote']); ?>
                                    </td>

                                    <td>

                                        <span class="status-badge
                                status-<?= strtolower($note['status']); ?>">

                                            <?= e($note['status']); ?>

                                        </span>

                                    </td>

                                    <td>

                                        <div class="action-wrap">

                                            <button class="action-button dropdown-trigger">
                                                Aksi <span>▼</span>
                                            </button>

                                            <div class="action-menu">

                                                <button class="menu-item view-note"
                                                    type="button"
                                                    data-title="<?= e($note['judul']); ?>"
                                                    data-author="<?= e($note['penulis']); ?>"
                                                    data-major="<?= e($note['jurusan']); ?>"
                                                    data-content="<?= e($note['isi']); ?>"
                                                    data-status="<?= e($note['status']); ?>"
                                                    data-reason="<?= e($note['alasan']); ?>">

                                                    Lihat

                                                </button>


                                                <button class="menu-item edit-note"
                                                    type="button"
                                                    data-id="<?= $note['id']; ?>"
                                                    data-title="<?= e($note['judul']); ?>"
                                                    data-author="<?= e($note['penulis']); ?>"
                                                    data-major="<?= e($note['jurusan']); ?>"
                                                    data-content="<?= e($note['isi']); ?>">

                                                    Edit

                                                </button>


                                                <form method="POST">

                                                    <input type="hidden"
                                                        name="action"
                                                        value="status">

                                                    <input type="hidden"
                                                        name="id"
                                                        value="<?= $note['id']; ?>">

                                                    <input type="hidden"
                                                        name="status"
                                                        value="<?= $note['status'] === 'Terbit'
                                                                    ? 'Draft'
                                                                    : 'Terbit'; ?>">

                                                    <button class="menu-item"
                                                        type="submit">

                                                        <?= $note['status'] === 'Terbit'
                                                            ? 'Jadikan Draft'
                                                            : 'Terbitkan'; ?>

                                                    </button>

                                                </form>


                                                <form method="POST"
                                                    onsubmit="return confirm('Hapus catatan ini?');">

                                                    <input type="hidden"
                                                        name="action"
                                                        value="delete">

                                                    <input type="hidden"
                                                        name="id"
                                                        value="<?= $note['id']; ?>">

                                                    <button class="menu-item delete-item"
                                                        type="submit">

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

            <button class="modal-close" data-close="add-modal">
                ×
            </button>

            <h2>Tambah catatan</h2>

            <form method="POST">

                <input type="hidden"
                    name="action"
                    value="add">

                <label>Judul catatan</label>
                <input type="text" name="judul" required>

                <label>Penulis</label>
                <input type="text" name="penulis" required>

                <label>Jurusan</label>
                <input type="text" name="jurusan" required>

                <label>Isi catatan</label>
                <textarea name="isi" rows="6" required></textarea>

                <button class="save-button" type="submit">
                    Tambahkan
                </button>

            </form>

        </div>

    </div>


    <!-- MODAL EDIT -->

    <div class="modal-overlay" id="edit-modal">

        <div class="modal">

            <button class="modal-close" data-close="edit-modal">
                ×
            </button>

            <h2>Edit catatan</h2>

            <form method="POST">

                <input type="hidden"
                    name="action"
                    value="edit">

                <input type="hidden"
                    name="id"
                    id="edit-id">

                <label>Judul catatan</label>
                <input type="text" name="judul" id="edit-title" required>

                <label>Penulis</label>
                <input type="text" name="penulis" id="edit-author" required>

                <label>Jurusan</label>
                <input type="text" name="jurusan" id="edit-major" required>

                <label>Isi catatan</label>
                <textarea name="isi"
                    id="edit-content"
                    rows="6"
                    required></textarea>

                <button class="save-button" type="submit">
                    Simpan perubahan
                </button>

            </form>

        </div>

    </div>


    <!-- MODAL LIHAT -->

    <div class="modal-overlay" id="view-modal">

        <div class="modal modal-large">

            <button class="modal-close" data-close="view-modal">
                ×
            </button>

            <h2 id="view-title"></h2>

            <p id="view-author"></p>

            <div class="view-content"
                id="view-content"></div>

            <div class="report-info"
                id="view-reason"></div>

        </div>

    </div>


    <script src="catatan.js"></script>

</body>

</html>