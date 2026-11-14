<?php
/**
 * View murni: hanya menampilkan data dari CatatanController::index().
 * Variabel di bawah dikirim oleh controller lewat render() + extract().
 *
 * @var string $page_title
 * @var string $active_menu
 * @var string $sidebar       HTML sidebar dari layouts/sidebar.php
 * @var string $user_name
 * @var string $user_role
 * @var string $user_initial
 * @var string $csrf
 * @var array<int, array<string, mixed>> $catatanSaya
 * @var array{pesan: string, galat: bool}|null $flash
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - <?= APP_NAME ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/layout_user.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/catatan_saya.css">
</head>

<body>
    <div class="app-container">

        <?= $sidebar ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Buka Menu">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= e($page_title) ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= e($user_name) ?></span>
                        <span class="user-role"><?= e($user_role) ?></span>
                    </div>
                    <div class="avatar"><?= e($user_initial) ?></div>
                </div>
            </header>

            <main class="content-area">
                <?php if ($flash): ?>
                    <div class="toast<?= $flash['galat'] ? ' toast-error' : '' ?>" role="status" data-auto>
                        <?= e($flash['pesan']) ?>
                    </div>
                <?php endif; ?>

                <div class="tabel-container">
                    <table class="tabel-catatan">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Visibilitas</th>
                                <th>Upvote</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($catatanSaya === []): ?>
                                <tr>
                                    <td colspan="5" class="td-kosong">
                                        Belum ada catatan.
                                        <a href="<?= BASE_URL ?>/?page=unggah-catatan">Unggah catatan pertamamu</a>.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($catatanSaya as $item): ?>
                                <tr>
                                    <td class="td-judul"><?= e($item['judul']) ?></td>
                                    <td><span class="badge <?= kelasTipe($item['tipe']) ?>"><?= e($item['tipe']) ?></span></td>
                                    <td>
                                        <?php if ($item['visibilitas'] === 'publik'): ?>
                                            <span class="badge badge-publik">Publik<?= $item['anonim'] ? ' · Anonim' : '' ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-pribadi">Pribadi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="upvote">
                                            <iconify-icon icon="lucide:thumbs-up"></iconify-icon>
                                            <?= (int) $item['upvote'] ?>
                                        </span>
                                    </td>
                                    <td class="td-aksi">
                                        <a href="<?= BASE_URL ?>/?page=unggah-catatan&amp;edit=<?= (int) $item['id'] ?>"
                                           class="btn-aksi btn-edit">Edit</a>
                                        <form action="<?= BASE_URL ?>/?page=hapus-catatan" method="POST" class="form-hapus"
                                              onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                            <button type="submit" class="btn-aksi btn-hapus">Hapus</button>
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

    <script src="<?= BASE_URL ?>/assets/layout_user/script.js"></script>
    <script src="<?= BASE_URL ?>/assets/catatan_saya/catatan_saya.js"></script>
</body>

</html>