<?php

$active_tab = $active_tab ?? 'masuk';
$error_messages = $error_messages ?? [];
$success_message = $success_message ?? null;

$isDaftar = $active_tab === 'daftar';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CatatIn - Autentikasi</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>

<body>

    <div class="card-container">

        <!-- Panel Kiri Branding -->
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
                &copy; <?= date('Y'); ?> CatatIn. All rights reserved.
            </div>

        </div>

        <!-- Panel Kanan -->
        <div class="right-panel">

            <!-- Tab Switcher -->
            <div class="tab-switcher">

                <button
                    type="button"
                    id="btn-masuk"
                    class="tab-btn <?= !$isDaftar ? 'active' : '' ?>">
                    Masuk
                </button>

                <button
                    type="button"
                    id="btn-daftar"
                    class="tab-btn <?= $isDaftar ? 'active' : '' ?>">
                    Daftar
                </button>

            </div>

            <!-- Notifikasi Error -->
            <?php if (!empty($error_messages)): ?>

                <div class="inline-error-container">

                    <?php foreach ($error_messages as $err): ?>

                        <div class="inline-error">

                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>

                            <span><?= htmlspecialchars($err); ?></span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <!-- Notifikasi Success -->
            <?php if (!empty($success_message)): ?>

                <div class="inline-success">
                    <span><?= htmlspecialchars($success_message); ?></span>
                </div>

            <?php endif; ?>


            <!-- FORM MASUK -->
            <div
                id="section-masuk"
                class="auth-section <?= !$isDaftar ? 'active' : '' ?>">

                <div class="form-header">

                    <h2>Selamat datang kembali</h2>

                    <p>Masuk untuk melanjutkan perjalanan belajarmu.</p>

                </div>

                <form method="POST" action="<?= BASE_URL ?>/?page=login">

                    <input type="hidden" name="action" value="login">

                    <div class="input-group">

                        <label for="login-email">Email / NIM</label>

                        <input
                            type="text"
                            id="login-email"
                            name="email"
                            placeholder="Masukkan email atau NIM"
                            required>

                    </div>

                    <div class="input-group">

                        <label for="login-password">Password</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="Masukkan password"
                                required>

                            <button
                                type="button"
                                class="toggle-password-btn"
                                data-target="login-password"
                                aria-label="Tampilkan password">
                            </button>

                        </div>

                    </div>

                    <button type="submit" class="btn-submit">
                        Masuk
                    </button>

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


            <!-- FORM DAFTAR -->
            <div
                id="section-daftar"
                class="auth-section <?= $isDaftar ? 'active' : '' ?>">

                <div class="form-header">

                    <h2>Buat Akun Baru</h2>

                    <p>Mulai bergabung dengan komunitas CatatIn.</p>

                </div>

                <form
                    id="register-form"
                    method="POST"
                    action="<?= BASE_URL ?>/?page=login">

                    <input type="hidden" name="action" value="register">

                    <div class="input-group">

                        <label for="reg-nama">Nama Lengkap</label>

                        <input
                            type="text"
                            id="reg-nama"
                            name="nama_lengkap"
                            placeholder="Masukkan nama lengkap"
                            required>

                    </div>

                    <div class="input-group">

                        <label for="reg-nim">NIM (10–20 Digit Angka)</label>

                        <input
                            type="text"
                            id="reg-nim"
                            name="nim"
                            placeholder="Masukkan NIM"
                            required>

                    </div>

                    <div class="input-group">

                        <label for="reg-email">Email Kampus</label>

                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            placeholder="contoh@student.ac.id"
                            required>

                    </div>

                    <div class="input-group">

                        <label for="reg-password">Password (Min. 8 Karakter)</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="reg-password"
                                name="password"
                                placeholder="Buat password"
                                required>

                            <button
                                type="button"
                                class="toggle-password-btn"
                                data-target="reg-password"
                                aria-label="Tampilkan password">
                            </button>

                        </div>

                    </div>

                    <div class="input-group">

                        <label for="reg-konfirmasi">Konfirmasi Password</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="reg-konfirmasi"
                                name="konfirmasi"
                                placeholder="Ulangi password"
                                required>

                            <button
                                type="button"
                                class="toggle-password-btn"
                                data-target="reg-konfirmasi"
                                aria-label="Tampilkan password">
                            </button>

                        </div>

                        <span id="konfirmasi-hint" class="field-hint">
                            Password tidak cocok!
                        </span>

                    </div>

                    <div class="checkbox-group">

                        <input
                            type="checkbox"
                            id="reg-setuju"
                            name="consent"
                            required>

                        <label for="reg-setuju">
                            Saya menyetujui ketentuan penggunaan.
                        </label>

                    </div>

                    <button type="submit" class="btn-submit">
                        Daftar Akun
                    </button>

                </form>

            </div>

        </div>

    </div>

    <script src="<?= BASE_URL ?>/js/auth.js" defer></script>

</body>

</html>