<?php
/**
 * @var string $page_title
 * @var string $active_menu
 * @var string $user_name
 * @var string $user_role
 * @var string $user_initial
 * @var string $csrf
 * @var array|null $flash
 * @var array $catatanSaya
 */

// Pastikan menu aktif di-set ke 'catatan_saya' agar tombol sidebar tersorot
$active_menu = $active_menu ?? 'catatan_saya';

if (!function_exists('e')) {
    function e($teks): string
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('kelasTipe')) {
    function kelasTipe(string $tipe): string
    {
        return match ($tipe) {
            'Soal Ujian' => 'badge-soal',
            'Modul'      => 'badge-modul',
            default      => 'badge-rangkuman',
        };
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title); ?> - CatatIn</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/catatan_saya.css">
</head>

<body>
    <div class="app-container">
        
        <!-- Panggil File Sidebar Terpisah di Sini -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Konten Utama -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>

                    <h1 class="page-title"><?= e($page_title); ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name); ?></span>
                        <span class="user-role"><?= e($user_role); ?></span>
                    </div>

                    <div class="avatar">
                        <?= e($user_initial); ?>
                    </div>
                </div>
            </header>

            <main class="content-area">
                <?php if (!empty($flash)): ?>
                    <div class="toast<?= !empty($flash['galat']) ? ' toast-error' : ''; ?>" role="status" data-auto>
                        <?= e($flash['pesan'] ?? ''); ?>
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
                            <?php if (empty($catatanSaya)): ?>
                                <tr>
                                    <td colspan="4" class="td-kosong">
                                        Belum ada catatan.
                                        <a href="<?= BASE_URL ?>/?page=unggah-catatan">
                                            Unggah catatan pertamamu
                                        </a>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($catatanSaya as $item): ?>
                                    <tr>
                                        <td class="td-judul">
                                            <?= e($item['judul'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php $tipe = $item['tipe'] ?? 'Rangkuman'; ?>
                                            <span class="badge <?= e(kelasTipe($tipe)); ?>">
                                                <?= e($tipe); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="upvote">
                                                <iconify-icon icon="lucide:thumbs-up"></iconify-icon>
                                                <?= (int) ($item['upvote'] ?? 0); ?>
                                            </span>
                                        </td>

                                        <td class="td-aksi">
                                            <a href="<?= BASE_URL ?>/?page=unggah-catatan&edit=<?= (int) $item['id']; ?>"
                                                class="btn-aksi btn-edit">
                                                Edit
                                            </a>

                                            <form action="<?= BASE_URL ?>/?page=hapus-catatan"
                                                method="POST"
                                                class="form-hapus"
                                                onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">

                                                <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                                                <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">

                                                <button type="submit" class="btn-aksi btn-hapus">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/js/catatan_saya.js"></script>
</body>

</html>