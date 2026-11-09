
<?php
// Nilai default
$page_title = $page_title ?? 'CatatIn';
$user_name = $user_name ?? 'Username';
$user_role = $user_role ?? 'User';

$user_initial = $user_initial
    ?? strtoupper(substr($user_name, 0, 1));

$is_admin = $is_admin ?? false;
?>

<?php if ($is_admin): ?>

    <!-- HEADER ADMIN -->
    <header class="top-header">

        <div class="header-left">
            <button type="button"
                    class="hamburger-btn"
                    id="hamburger-btn"
                    aria-label="Buka Menu">
                <iconify-icon icon="lucide:menu"></iconify-icon>
            </button>

            <h1 class="page-title">
                <?= htmlspecialchars($page_title) ?>
            </h1>
        </div>

        <div class="user-profile">

            <div class="user-info">
                <span class="user-name">
                    <?= htmlspecialchars($user_name) ?>
                </span>

                <span class="user-role">
                    <?= htmlspecialchars($user_role) ?>
                </span>
            </div>

            <div class="user-avatar">
                <?= htmlspecialchars($user_initial) ?>
            </div>

        </div>

    </header>

<?php else: ?>

    <!-- HEADER USER -->
    <header class="top-header">

        <div class="header-left">

            <button type="button"
                    class="menu-toggle"
                    id="menuToggle"
                    aria-label="Buka Menu">
                <iconify-icon icon="lucide:menu"></iconify-icon>
            </button>

            <h1 class="page-title">
                <?= htmlspecialchars($page_title) ?>
            </h1>

        </div>

        <div class="user-profile">

            <div class="user-info">
                <span class="user-name">
                    <?= htmlspecialchars($user_name) ?>
                </span>

                <span class="user-role">
                    <?= htmlspecialchars($user_role) ?>
                </span>
            </div>

            <div class="avatar">
                <?= htmlspecialchars($user_initial) ?>
            </div>

        </div>

    </header>

<?php endif; ?>
