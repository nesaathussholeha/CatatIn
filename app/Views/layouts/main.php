<?php

// DEFAULT VARIABLE
$page_title  = $page_title ?? 'CatatIn';
$user_name   = $user_name ?? 'Username';
$user_role   = $user_role ?? 'User';
$active_menu = $active_menu ?? 'dashboard';

$user_initial = $user_initial
    ?? strtoupper(substr($user_name, 0, 1));

$content  = $content ?? '';
$page_css = $page_css ?? '';
$page_js  = $page_js ?? '';

// CEK ROLE USER
$is_admin = (strtolower($user_role) === 'admin');


// PILIH CSS DAN JS LAYOUT
$layout_css = $is_admin
    ? 'layout_admin.css'
    : 'layout_user.css';

$layout_js = $is_admin
    ? 'layout_admin.js'
    : 'layout_user.js';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($page_title) ?> - CatatIn
    </title>

    <!-- GOOGLE FONTS -->
    <?php if ($is_admin): ?>

        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">

    <?php else: ?>

        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <?php endif; ?>


    <!-- ICONIFY -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>


    <!-- CSS LAYOUT -->
    <link rel="stylesheet"
        href="<?= BASE_URL ?>/css/<?= $layout_css ?>">


    <!-- CSS KHUSUS HALAMAN -->
    <?php if (!empty($page_css)): ?>

        <link rel="stylesheet"
            href="<?= BASE_URL ?>/css/<?= htmlspecialchars($page_css) ?>">

    <?php endif; ?>

</head>

<body>

    <?php if ($is_admin): ?>

        <!-- LAYOUT ADMIN -->
        <div class="admin-wrapper">

            <?php require VIEW_PATH . '/partials/sidebar.php'; ?>

            <main class="main-content">

                <?php require VIEW_PATH . '/partials/header.php'; ?>

                <section class="content-area">
                    <?= $content ?>
                </section>

            </main>

        </div>


    <?php else: ?>

        <!-- LAYOUT USER -->
        <div class="app-container">

            <?php require VIEW_PATH . '/partials/sidebar.php'; ?>

            <div class="main-wrapper">

                <?php require VIEW_PATH . '/partials/header.php'; ?>

                <main class="content-area">
                    <div>
                        <?= $content ?>
                    </div>
                </main>

            </div>

        </div>

    <?php endif; ?>


    <!-- JAVASCRIPT LAYOUT -->
    <script src="<?= BASE_URL ?>/js/<?= $layout_js ?>"></script>


    <!-- JAVASCRIPT KHUSUS HALAMAN -->
    <?php if (!empty($page_js)): ?>

        <script src="<?= BASE_URL ?>/js/<?= htmlspecialchars($page_js) ?>"></script>

    <?php endif; ?>

</body>

</html>