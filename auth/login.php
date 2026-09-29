<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard_user.php");
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $VALID_EMAIL = "sarah@gmail.com";
    $VALID_PASSWORD = "123456";

    if ($email === $VALID_EMAIL && $password === $VALID_PASSWORD) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = "Sarah";

        header("Location: ../dashboard_user.php");
        exit();
    } else {
        $error_message = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Notes Hub - Login</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>

<body>
    <main class="login-card">
        <section class="intro">
            <header class="brand">
                <iconify-icon icon="ph:book-bookmark-fill" class="book-icon"></iconify-icon>
                <h1>Study Notes Hub</h1>
            </header>

            <article class="intro-text">
                <h2>Belajar Bersama,<br><strong>Lebih Bermakna</strong></h2>
                <p>Wadah untuk berbagi catatan, modul, dan latihan soal antarjurusan.</p>
            </article>

            <figure class="illustration">
                <img src="../images/Studi_Notes.png" alt="Ilustrasi perlengkapan belajar">
            </figure>
        </section>

        <section class="login">
            <header class="login-header">
                <h2>Selamat Datang Kembali!</h2>
                <p>Masuk ke akun Study Notes Hub untuk melanjutkan perjalanan belajarmu.</p>
            </header>

            <?php if (!empty($error_message)): ?>
                <div class="alert-error" style="color: #d32f2f; background-color: #ffebee; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Contoh: sarah@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <div class="label-wrapper">
                        <label for="password">Password</label>
                        <a href="#" id="forgotPassword">Lupa password?</a>
                    </div>
                    <div class="password-box">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" minlength="6" required>
                        <button type="button" id="togglePassword" aria-label="Tampilkan password">
                            <iconify-icon icon="lucide:eye" id="eyeIcon"></iconify-icon>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">Masuk</button>

                <div class="or">
                    <span></span> atau <span></span>
                </div>

                <button type="button" class="google-button" id="googleLogin">
                    <iconify-icon icon="flat-color-icons:google" width="22" height="22"></iconify-icon>
                    Masuk dengan Google
                </button>
            </form>

            <footer class="register">
                Belum punya akun? <a href="#">Daftar sekarang</a>
            </footer>

            <p id="message" class="message" aria-live="polite"></p>
        </section>
    </main>

    <footer class="page-footer">
        &copy; 2026 Kelompok 5
    </footer>

    <script src="login.js"></script>
</body>

</html>