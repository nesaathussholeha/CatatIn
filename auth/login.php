<?php
session_start();

// Directory & File Data Pengguna JSON
$data_dir  = __DIR__ . "/data";
$data_file = $data_dir . "/users.json";

if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode([]));
}

// Ambil error & success dari SESSION (Flash Message)
$error_messages  = $_SESSION['error_messages'] ?? [];
$success_message = $_SESSION['success_message'] ?? '';

// LANGSUNG UNSET agar hilang saat halaman di-refresh / dimuat ulang
unset($_SESSION['error_messages'], $_SESSION['success_message']);

// Tab mana yang aktif pertama kali (default: masuk)
$active_tab = $_SESSION['active_tab'] ?? 'masuk';
unset($_SESSION['active_tab']);

// Jika pengguna sudah login
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/admin.php");
    } else {
        header("Location: ../dashboard_user/dashboard.php");
    }
    exit();
}

// --- LOGIKA FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];

    // LOGIKA LOGIN / MASUK
    if ($action === 'login') {
        $identifier = trim($_POST['email'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            $errors[] = "Email/NIM dan Password wajib diisi.";
        } else {
            $users = json_decode(file_get_contents($data_file), true) ?: [];
            $found_user = null;

            foreach ($users as $u) {
                if ($u["email"] === $identifier || $u["nim"] === $identifier) {
                    $found_user = $u;
                    break;
                }
            }

            if ($found_user && password_verify($password, $found_user["password"])) {
                if (isset($found_user["is_active"]) && !$found_user["is_active"]) {
                    $errors[] = "Akun Anda sedang dinonaktifkan.";
                } else {
                    $_SESSION["user_id"]      = $found_user["id"];
                    $_SESSION["nama_lengkap"] = $found_user["nama_lengkap"];
                    $_SESSION["role"]         = $found_user["role"];

                    if ($found_user["role"] === "admin") {
                        header("Location: ../admin/admin.php");
                    } else {
                        header("Location: ../dashboard_user/dashboard.php");
                    }
                    exit();
                }
            } else {
                $errors[] = "Email/NIM atau Password salah.";
            }
        }

        // Redirect balik jika ada error pada Login
        if (!empty($errors)) {
            $_SESSION['error_messages'] = $errors;
            $_SESSION['active_tab']     = 'masuk';
            header("Location: login.php");
            exit();
        }
    }

    // LOGIKA REGISTER / DAFTAR
    elseif ($action === 'register') {
        $nama                = trim($_POST["nama"] ?? "");
        $nim                 = trim($_POST["nim"] ?? "");
        $email               = trim($_POST["email"] ?? "");
        $password            = $_POST["password"] ?? "";
        $konfirmasi_password = $_POST["konfirmasi_password"] ?? "";
        $setuju              = isset($_POST["setuju"]);

        if ($nama === "") {
            $errors[] = "Nama lengkap wajib diisi.";
        }
        if ($nim === "" || !preg_match("/^[0-9]{10,20}$/", $nim)) {
            $errors[] = "NIM wajib diisi dan hanya berupa angka (10–20 digit).";
        }
        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password minimal 8 karakter.";
        }
        if ($password !== $konfirmasi_password) {
            $errors[] = "Konfirmasi password tidak sama dengan password.";
        }
        if (!$setuju) {
            $errors[] = "Kamu harus menyetujui ketentuan penggunaan.";
        }

        // Cek Duplikasi NIM / Email
        if (empty($errors)) {
            $users = json_decode(file_get_contents($data_file), true) ?: [];

            foreach ($users as $u) {
                if ($u["nim"] === $nim || $u["email"] === $email) {
                    $errors[] = "NIM atau email sudah terdaftar. Silakan login.";
                    break;
                }
            }
        }

        // Simpan Data jika Validasi Lolos
        if (empty($errors)) {
            $users[] = [
                "id"           => uniqid(),
                "nama_lengkap" => $nama,
                "nim"          => $nim,
                "email"        => $email,
                "password"     => password_hash($password, PASSWORD_DEFAULT),
                "role"         => "mahasiswa",
                "total_upvote" => 0,
                "badge"        => null,
                "is_active"    => true,
                "created_at"   => date("Y-m-d H:i:s"),
            ];

            file_put_contents($data_file, json_encode($users, JSON_PRETTY_PRINT));

            $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan masuk ke akun Anda.";
            $_SESSION['active_tab']      = 'masuk';
        } else {
            $_SESSION['error_messages'] = $errors;
            $_SESSION['active_tab']     = 'daftar';
        }

        // SELALU REDIRECT SETELAH POST UNTUK MENCEGAH RESUBMISSION SAAT REFRESH
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
    <title>CatatIn - Autentikasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
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
                &copy; <?php echo date('Y'); ?> CatatIn. All rights reserved.
            </div>
        </div>

        <!-- Panel Kanan Form Dynamic Switch -->
        <div class="right-panel">

            <div class="tab-switcher">
                <button type="button" class="tab-btn <?php echo $active_tab === 'masuk' ? 'active' : ''; ?>" id="btn-masuk">Masuk</button>
                <button type="button" class="tab-btn <?php echo $active_tab === 'daftar' ? 'active' : ''; ?>" id="btn-daftar">Daftar</button>
            </div>

            <!-- Pesan Notifikasi Global -->
            <?php if (!empty($error_messages)): ?>
                <div class="inline-error-container">
                    <?php foreach ($error_messages as $err): ?>
                        <div class="inline-error">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?php echo htmlspecialchars($err); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="inline-success">
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- ================= FORM MASUK ================= -->
            <div id="section-masuk" class="auth-section <?php echo $active_tab === 'masuk' ? 'active' : ''; ?>">
                <div class="form-header">
                    <h2>Selamat datang kembali</h2>
                    <p>Masuk untuk melanjutkan perjalanan belajarmu.</p>
                </div>

                <form action="login.php" method="POST" id="login-form" autocomplete="off">
                    <input type="hidden" name="action" value="login">

                    <div class="input-group">
                        <label for="login-email">Email / NIM</label>
                        <input type="text" id="login-email" name="email" placeholder="Masukkan email atau NIM" required>
                    </div>

                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="login-password" name="password" placeholder="Masukkan password" required>
                            <button type="button" class="toggle-password-btn" data-target="login-password" aria-label="Tampilkan password"></button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Masuk</button>
                </form>

                <div class="divider"><span>atau</span></div>

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

            <!-- ================= FORM DAFTAR ================= -->
            <div id="section-daftar" class="auth-section <?php echo $active_tab === 'daftar' ? 'active' : ''; ?>">
                <div class="form-header">
                    <h2>Buat Akun Baru</h2>
                    <p>Mulai bergabung dengan komunitas CatatIn.</p>
                </div>

                <form action="login.php" method="POST" id="register-form" autocomplete="off">
                    <input type="hidden" name="action" value="register">

                    <div class="input-group">
                        <label for="reg-nama">Nama Lengkap</label>
                        <input type="text" id="reg-nama" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="input-group">
                        <label for="reg-nim">NIM (10–20 Digit Angka)</label>
                        <input type="text" id="reg-nim" name="nim" placeholder="Masukkan NIM" required>
                    </div>

                    <div class="input-group">
                        <label for="reg-email">Email Kampus</label>
                        <input type="email" id="reg-email" name="email" placeholder="contoh@student.ac.id" required>
                    </div>

                    <div class="input-group">
                        <label for="reg-password">Password (Min. 8 Karakter)</label>
                        <div class="password-wrapper">
                            <input type="password" id="reg-password" name="password" placeholder="Buat password" required>
                            <button type="button" class="toggle-password-btn" data-target="reg-password" aria-label="Tampilkan password"></button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-konfirmasi">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="reg-konfirmasi" name="konfirmasi_password" placeholder="Ulangi password" required>
                            <button type="button" class="toggle-password-btn" data-target="reg-konfirmasi" aria-label="Tampilkan password"></button>
                        </div>
                        <span id="konfirmasi-hint" class="field-hint">Password tidak cocok!</span>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="reg-setuju" name="setuju" required>
                        <label for="reg-setuju">Saya menyetujui ketentuan penggunaan.</label>
                    </div>

                    <button type="submit" class="btn-submit">Daftar Akun</button>
                </form>
            </div>

        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>