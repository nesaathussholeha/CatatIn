<?php

$data_dir  = __DIR__ . "/data";
$data_file = $data_dir . "/users.json";

if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode([]));
}

$errors  = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Ambil & bersihkan input
    $nama     = trim($_POST["nama"] ?? "");
    $nim      = trim($_POST["nim"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $konfirmasi_password = $_POST["konfirmasi_password"] ?? "";
    $setuju   = isset($_POST["setuju"]);

    // 2. Validasi 
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

    // 3. Baca data pengguna yang sudah ada
    $users = [];
    if (empty($errors)) {
        $users = json_decode(file_get_contents($data_file), true) ?: [];

        foreach ($users as $u) {
            if ($u["nim"] === $nim || $u["email"] === $email) {
                $errors[] = "NIM atau email sudah terdaftar. Silakan login.";
                break;
            }
        }
    }

    // 4. Simpan pengguna baru jika tidak ada error
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
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Study Notes Hub - Daftar</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth">
      <div class="auth-left">
        <div class="brand">CatatIn</div>
        <h1>Mulai Berbagi,<br><em>Mulai Belajar.</em></h1>
        <p>Buat akun mahasiswa untuk mengunggah catatan, memberi upvote, dan membangun reputasimu di kampus.</p>
        <ul>
          <li><i></i> Gratis, tanpa paywall</li>
          <li><i></i> Materi tersusun rapi per jurusan &amp; matkul</li>
          <li><i></i> Dapatkan badge dari kontribusimu</li>
        </ul>
      </div>
      <div class="auth-right">
        <div class="card">
          <div class="auth-tabs">
            <a href="login.php">Masuk</a>
            <a href="register.php" class="on">Daftar</a>
          </div>

          <h1>Buat akun baru</h1>
          <p class="sub">Isi data berikut untuk mulai bergabung.</p>

          <?php if ($success): ?>
            <div class="alert alert-success">
              Pendaftaran berhasil! Silakan <a href="login.php">login</a> menggunakan akun barumu.
            </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
              <strong>Pendaftaran gagal:</strong>
              <ul>
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!$success): ?>
          <form method="POST" action="register.php">
            <label>Nama lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>

            <label>NIM</label>
            <input type="text" name="nim" value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" required>

            <label>Email kampus</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <div class="row">
              <div>
                <label>Password</label>
                <input type="password" name="password" required>
              </div>
              <div>
                <label>Konfirmasi password</label>
                <input type="password" name="konfirmasi_password" required>
                <span id="konfirmasi-hint" class="field-hint">Password tidak cocok</span>
              </div>
            </div>

            <div class="check">
              <input type="checkbox" name="setuju" id="setuju">
              <label for="setuju" style="margin:0;font-weight:400;">Saya menyetujui ketentuan penggunaan</label>
            </div>

            <button type="submit">Daftar sekarang</button>
          </form>
          <?php endif; ?>

          <p class="footer-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>