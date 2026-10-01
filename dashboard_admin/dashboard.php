<?php
session_start();

/* ==============================
   DATA AWAL
================================ */

if (!isset($_SESSION['catatan_data'])) {
    $_SESSION['catatan_data'] = [
        [
            'id' => 1,
            'judul' => 'Ringkasan Struktur Data — Binary Tree',
            'penulis' => 'Sarah Faradila',
            'jurusan' => 'Informatika',
            'upvote' => 128,
            'status' => 'Terbit',
            'isi' => 'Catatan mengenai struktur data Binary Tree, jenis tree, traversal, dan penerapannya.',
            'alasan' => ''
        ],
        [
            'id' => 2,
            'judul' => 'Rangkuman Basis Data Relasional',
            'penulis' => 'Nazma Fairuz M.',
            'jurusan' => 'Sistem Informasi',
            'upvote' => 94,
            'status' => 'Terbit',
            'isi' => 'Rangkuman database relasional, tabel, primary key, foreign key, dan relasi.',
            'alasan' => ''
        ],
        [
            'id' => 3,
            'judul' => 'Catatan Kalkulus II — Integral Lipat',
            'penulis' => 'Bagas Wicaksono',
            'jurusan' => 'Teknik Informatika',
            'upvote' => 61,
            'status' => 'Dilaporkan',
            'isi' => 'Pembahasan integral lipat dua dan tiga beserta contoh soal dan penyelesaiannya.',
            'alasan' => 'Terdapat bagian catatan yang dianggap kurang sesuai dengan materi.'
        ],
        [
            'id' => 4,
            'judul' => 'Ringkasan Jaringan Komputer — OSI Layer',
            'penulis' => 'Dewi Anjani',
            'jurusan' => 'Informatika',
            'upvote' => 45,
            'status' => 'Terbit',
            'isi' => 'Ringkasan tujuh layer pada model OSI dan fungsi setiap layer.',
            'alasan' => ''
        ]
    ];
}

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
   AKSI PENGGUNA
================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['user_action'])) {

        $id = (int) $_POST['id'];
        $action = $_POST['user_action'];

        foreach ($_SESSION['pengguna_data'] as $key => $user) {

            if ($user['id'] == $id) {

                if ($action === 'block') {
                    $_SESSION['pengguna_data'][$key]['status'] = 'Diblokir';
                }

                if ($action === 'unblock') {
                    $_SESSION['pengguna_data'][$key]['status'] = 'Aktif';
                }

                if ($action === 'delete') {
                    unset($_SESSION['pengguna_data'][$key]);
                    $_SESSION['pengguna_data'] = array_values($_SESSION['pengguna_data']);
                }

                break;
            }
        }

        header('Location: dashboard.php');
        exit;
    }


    /* ==============================
       AKSI CATATAN
    ================================ */

    if (isset($_POST['note_action'])) {

        $id = (int) $_POST['id'];
        $action = $_POST['note_action'];

        foreach ($_SESSION['catatan_data'] as $key => $note) {

            if ($note['id'] == $id) {

                if ($action === 'publish') {
                    $_SESSION['catatan_data'][$key]['status'] = 'Terbit';
                    $_SESSION['catatan_data'][$key]['alasan'] = '';
                }

                if ($action === 'delete') {
                    unset($_SESSION['catatan_data'][$key]);
                    $_SESSION['catatan_data'] = array_values($_SESSION['catatan_data']);
                }

                break;
            }
        }

        header('Location: dashboard.php');
        exit;
    }
}


/* ==============================
   DATA DASHBOARD
================================ */

$catatan = $_SESSION['catatan_data'];
$pengguna = $_SESSION['pengguna_data'];

$totalCatatan = count($catatan);
$totalPengguna = count($pengguna);

$totalUpvote = 0;

foreach ($catatan as $item) {
    $totalUpvote += (int) $item['upvote'];
}

$laporan = array_filter($catatan, function ($item) {
    return $item['status'] === 'Dilaporkan';
});


function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

$namaAdmin = $_SESSION['user_name'] ?? 'Administrator';
$roleAdmin = $_SESSION['role'] ?? 'Admin';
$avatar = strtoupper(substr($namaAdmin, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CatatIn - Dashboard Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="dashboard.css">

</head>

<body>

<div class="admin-wrapper">

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR -->
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

            <a href="dashboard.php" class="nav-item active">
                Dashboard
            </a>

            <a href="../kelola_catatan/catatan.php" class="nav-item">
                Kelola Catatan
            </a>

            <a href="../kelola_pengguna/pengguna.php" class="nav-item">
                Kelola Pengguna
            </a>

            <a href="#" class="nav-item">
                Master Data
            </a>

            <a href="#" class="nav-item">
                Kelola Laporan
            </a>

            <a href="#" class="nav-item btn-logout"
               onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                Keluar
            </a>

        </nav>

    </aside>


    <!-- CONTENT -->
    <main class="main-content">

        <header class="top-header">

            <div class="header-left">

                <button class="hamburger-btn" id="hamburger-btn">
                    ☰
                </button>

                <h1 class="page-title">
                    Ringkasan sistem
                </h1>

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


        <!-- STATISTIK -->

        <section class="stat-grid">

            <div class="stat-card">
                <strong><?= $totalCatatan; ?></strong>
                <span>Total catatan</span>
            </div>

            <div class="stat-card">
                <strong><?= $totalPengguna; ?></strong>
                <span>Total pengguna</span>
            </div>

            <div class="stat-card">
                <strong><?= $totalUpvote; ?></strong>
                <span>Total upvote</span>
            </div>

        </section>


        <!-- PENGGUNA -->

        <section class="content-section">

            <div class="section-heading">

                <h2>Kelola pengguna</h2>

                <a href="../kelola_pengguna/pengguna.php">
                    Lihat semua
                </a>

            </div>


            <div class="table-card">

                <div class="table-responsive">

                    <table>

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

                        <?php foreach (array_slice($pengguna, 0, 5) as $user): ?>

                            <tr>

                                <td><?= e($user['nama']); ?></td>

                                <td><?= e($user['nim']); ?></td>

                                <td><?= e($user['jurusan']); ?></td>

                                <td>

                                    <span class="status-user
                                    <?= $user['status'] === 'Aktif'
                                        ? 'status-active'
                                        : 'status-blocked'; ?>">

                                        <?= e($user['status']); ?>

                                    </span>

                                </td>

                                <td>

                                    <div class="action-wrap">

                                        <button class="action-button dropdown-trigger"
                                                type="button">

                                            Aksi <span>▼</span>

                                        </button>

                                        <div class="action-menu">

                                            <button class="menu-item view-user"
                                                    type="button"
                                                    data-name="<?= e($user['nama']); ?>"
                                                    data-nim="<?= e($user['nim']); ?>"
                                                    data-jurusan="<?= e($user['jurusan']); ?>"
                                                    data-status="<?= e($user['status']); ?>">

                                                Lihat

                                            </button>

                                            <form method="POST">

                                                <input type="hidden"
                                                       name="id"
                                                       value="<?= $user['id']; ?>">

                                                <input type="hidden"
                                                       name="user_action"
                                                       value="<?= $user['status'] === 'Aktif'
                                                           ? 'block'
                                                           : 'unblock'; ?>">

                                                <button class="menu-item"
                                                        type="submit">

                                                    <?= $user['status'] === 'Aktif'
                                                        ? 'Blokir'
                                                        : 'Buka blokir'; ?>

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

            </div>

        </section>


        <!-- LAPORAN -->

        <section class="report-card">

            <div>

                <h2>Catatan dilaporkan</h2>

                <p>
                    Terdapat <strong><?= count($laporan); ?></strong>
                    catatan yang perlu diperiksa.
                </p>

            </div>

            <button class="primary-button" id="report-button">
                Periksa laporan
            </button>

        </section>


        <section class="report-list" id="report-list">

            <div class="section-heading">
                <h2>Daftar laporan</h2>
            </div>

            <?php if (count($laporan) > 0): ?>

                <?php foreach ($laporan as $note): ?>

                    <div class="reported-item">

                        <div class="reported-info">

                            <h3><?= e($note['judul']); ?></h3>

                            <p>
                                Penulis: <?= e($note['penulis']); ?>
                            </p>

                            <p>
                                Alasan:
                                <?= e($note['alasan']); ?>
                            </p>

                        </div>

                        <div class="report-actions">

                            <button class="secondary-button view-note"
                                    type="button"
                                    data-title="<?= e($note['judul']); ?>"
                                    data-author="<?= e($note['penulis']); ?>"
                                    data-content="<?= e($note['isi']); ?>"
                                    data-reason="<?= e($note['alasan']); ?>">

                                Lihat isi

                            </button>

                            <form method="POST">

                                <input type="hidden"
                                       name="id"
                                       value="<?= $note['id']; ?>">

                                <input type="hidden"
                                       name="note_action"
                                       value="publish">

                                <button class="primary-button">
                                    Terbitkan
                                </button>

                            </form>

                            <form method="POST"
                                  onsubmit="return confirm('Hapus catatan ini?');">

                                <input type="hidden"
                                       name="id"
                                       value="<?= $note['id']; ?>">

                                <input type="hidden"
                                       name="note_action"
                                       value="delete">

                                <button class="danger-button">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty-state">
                    Tidak ada catatan yang dilaporkan.
                </div>

            <?php endif; ?>

        </section>

    </main>

</div>


<!-- MODAL USER -->

<div class="modal-overlay" id="user-modal">

    <div class="modal">

        <button class="modal-close" data-close="user-modal">
            ×
        </button>

        <h2>Detail pengguna</h2>

        <div class="detail-list">

            <div>
                <span>Nama</span>
                <strong id="modal-user-name"></strong>
            </div>

            <div>
                <span>NIM</span>
                <strong id="modal-user-nim"></strong>
            </div>

            <div>
                <span>Jurusan</span>
                <strong id="modal-user-jurusan"></strong>
            </div>

            <div>
                <span>Status</span>
                <strong id="modal-user-status"></strong>
            </div>

        </div>

    </div>

</div>


<!-- MODAL CATATAN -->

<div class="modal-overlay" id="note-modal">

    <div class="modal modal-large">

        <button class="modal-close" data-close="note-modal">
            ×
        </button>

        <h2 id="modal-note-title"></h2>

        <p class="modal-author" id="modal-note-author"></p>

        <div class="note-content"
             id="modal-note-content"></div>

        <div class="report-box">

            <strong>Alasan laporan</strong>

            <p id="modal-note-reason"></p>

        </div>

    </div>

</div>


<script src="dashboard.js"></script>

</body>
</html>