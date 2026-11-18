<?php
$page_title = $page_title ?? 'Catatan Saya';
$user_name = $user_name ?? 'Username';
$user_role = $user_role ?? 'Role';
$user_initial = $user_initial ?? 'U';

$flash = $flash ?? null;
$catatanSaya = $catatanSaya ?? [];

if (!function_exists('e')) {
    function e(?string $teks): string
    {
        return htmlspecialchars(
            (string) $teks,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('kelasTipe')) {
    function kelasTipe(string $tipe): string
    {
        return match ($tipe) {
            'Soal Ujian' => 'badge-soal',
            'Modul' => 'badge-modul',
            default => 'badge-rangkuman'
        };
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> - CatatIn</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="catatan_saya.css">
    <script src="catatan_saya.js"></script>
</head>

<body>

    <div class="app-container">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">
            <div>
                <div class="sidebar-header">
                    <div class="brand">
                        <span>CatatIn</span>
                    </div>

                    <button class="close-sidebar-btn" id="closeSidebar"
                        aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="../dashboard_user/dashboard.php" class="nav-link">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="../../upload/upload.php" class="nav-link">
                            Unggah Catatan
                        </a>
                    </li>

                    <li>
                        <a href="../../catatan_saya/catatan_saya.php"
                            class="nav-link active">
                            Catatan Saya
                        </a>
                    </li>

                    <li>
                        <a href="../playlist/playlist.php" class="nav-link">
                            Koleksi
                        </a>
                    </li>

                    <li>
                        <a href="../Pengingat/tugas.php" class="nav-link">
                            Tugas
                        </a>
                    </li>

                    <li>
                        <a href="../profile/profile.php" class="nav-link">
                            Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="../logout.php" class="nav-link logout">
                    Keluar
                </a>
            </div>
        </aside>

        <div class="main-wrapper">

            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>

                    <h1 class="page-title">
                        <?= e($page_title) ?>
                    </h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name">
                            <?= e($user_name) ?>
                        </span>

                        <span class="user-role">
                            <?= e($user_role) ?>
                        </span>
                    </div>

                    <div class="avatar">
                        <?= e($user_initial) ?>
                    </div>
                </div>
            </header>

            <main class="content-area">

                <?php if ($flash): ?>
                    <div class="toast<?= $flash['galat'] ? ' toast-error' : '' ?>"
                        role="status" data-auto>
                        <?= e($flash['pesan']) ?>
                    </div>
                <?php endif; ?>

                <div class="tabel-container">

                    <table class="tabel-catatan">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Upvote</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($catatanSaya === []): ?>
                                <tr>
                                    <td colspan="4" class="td-kosong">
                                        Belum ada catatan.
                                        <a href="../../upload/upload.php">
                                            Unggah catatan pertamamu
                                        </a>.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($catatanSaya as $item): ?>
                                <tr>

                                    <td class="td-judul">
                                        <?= e($item['judul']) ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= kelasTipe($item['tipe']) ?>">
                                            <?= e($item['tipe']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="upvote">
                                            <iconify-icon icon="lucide:thumbs-up"></iconify-icon>
                                            <?= (int) $item['upvote'] ?>
                                        </span>
                                    </td>

                                    <td class="td-aksi">

                                        <a href="../../upload/upload.php?edit=<?= (int) $item['id'] ?>"
                                            class="btn-aksi btn-edit">
                                            Edit
                                        </a>

                                        <form action="hapus.php" method="POST"
                                            class="form-hapus"
                                            onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">

                                            <input type="hidden" name="csrf"
                                                value="<?= e($_SESSION['csrf']) ?>">

                                            <input type="hidden" name="id"
                                                value="<?= (int) $item['id'] ?>">

                                            <button type="submit"
                                                class="btn-aksi btn-hapus">
                                                Hapus
                                            </button>

                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>

            </main>
        </div>
    </div>

    <script src="../layout_user/script.js"></script>
    <script src="../../catatan_saya/catatan_saya.js"></script>

</body>

</html>