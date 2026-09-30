<?php
session_start();

$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/admin.php");
    } else {
        header("Location: ../dashboard_user/dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_input = trim($_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? '';

    $dummy_users = [
        [
            'id'       => 1,
            'name'     => 'User biasa',
            'email'    => 'user@gmail.com',
            'password' => '123456',
            'role'     => 'user',
            'redirect' => '../dashboard_user/dashboard.php'
        ],
        [
            'id'       => 2,
            'name'     => 'Administrator',
            'email'    => 'admin@gmail.com',
            'password' => '123456',
            'role'     => 'admin',
            'redirect' => '../admin/admin.php'
        ]
    ];

    $is_authenticated = false;

    foreach ($dummy_users as $account) {
        if ($email_input === $account['email'] && $password_input === $account['password']) {
            $_SESSION['user_id']   = $account['id'];
            $_SESSION['user_name'] = $account['name'];
            $_SESSION['role']      = $account['role'];

            $is_authenticated = true;
            header("Location: " . $account['redirect']);
            exit();
        }
    }

    if (!$is_authenticated) {
        $_SESSION['error_message'] = "Email atau password salah!";
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatatIn - Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="card-container">
        <div class="left-panel">
            <div>
                <div class="logo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>CatatIn</span>
                </div>

                <h1 class="headline">
                    Belajar Bersama,<br>
                    <span class="highlight">Lebih Bermakna.</span>
                </h1>

                <p class="description">
                    Wadah untuk berbagi catatan, modul, dan latihan soal antarjurusan.
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <span>Akses rangkuman materi lengkap</span>
                    </div>
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <span>Berbagi dan temukan catatan terbaik</span>
                    </div>
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <span>Naik lewat upvote dari sesama mahasiswa</span>
                    </div>
                </div>
            </div>

            <div class="copyright">
                &copy; <?php echo date('Y'); ?> CatatIn. All rights reserved.
            </div>
        </div>

        <div class="right-panel">

            <div class="tab-switcher">
                <button type="button" class="tab-btn active" id="btn-masuk">Masuk</button>
                <button type="button" class="tab-btn" id="btn-daftar">Daftar</button>
            </div>

            <div class="form-header">
                <h2>Selamat datang kembali</h2>
                <p>Masuk untuk melanjutkan perjalanan belajarmu.</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="inline-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="login-form" autocomplete="off">
                <div class="input-group">
                    <label for="email">Email / Username</label>
                    <input type="text" id="email" name="email" class="<?php echo !empty($error_message) ? 'is-invalid' : ''; ?>" placeholder="Masukkan email atau username" autocomplete="off" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="<?php echo !empty($error_message) ? 'is-invalid' : ''; ?>" placeholder="Masukkan password" autocomplete="off" required>
                        <button type="button" id="toggle-password" class="toggle-password-btn" aria-label="Tampilkan password"> </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Masuk</button>
            </form>

            <div class="divider">
                <span>atau</span>
            </div>

            <button type="button" class="btn-google" id="btn-google">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                Masuk dengan Google
            </button>

        </div>
    </div>

    <script src="login.js"></script>
</body>

</html>