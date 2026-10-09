<?php

$data_dir  = __DIR__ . "/data";
$data_file = $data_dir . "/users.json";

// Pastikan folder & file penyimpanan tersedia
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode([]));
}

$errors  = [];
$success = false;

$daftar_jurusan = ["Teknik Informatika", "Teknik Komputer", "Sistem Informasi", "Teknologi Informasi", "Pendidikan Teknologi Informasi"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Ambil & bersihkan input
    $nama     = trim($_POST["nama"] ?? "");
    $nim      = trim($_POST["nim"] ?? "");
    $jurusan  = trim($_POST["jurusan"] ?? "");
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
    if (!in_array($jurusan, $daftar_jurusan)) {
        $errors[] = "Jurusan tidak valid.";
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

    // 3. Baca data pengguna yang sudah ada, cek NIM/email duplikat
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
            "jurusan"      => $jurusan,
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
    <style>
        :root {
            --navy: #171A3D;
            --indigo: #4C5FE0;
            --indigo-dk: #3B4BC7;
            --bg: #EEF1FB;
            --slate: #64748B;
            --border: #E1E4F2;
            --danger: #E15353;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            max-width: 440px;
            width: 100%;
            padding: 36px 34px;
            box-shadow: 0 4px 24px rgba(23, 26, 61, .08);
        }

        h1 {
            font-family: 'Poppins';
            font-weight: 700;
            font-size: 24px;
            margin: 0 0 6px;
        }

        p.sub {
            color: var(--slate);
            font-size: 13.5px;
            margin: 0 0 22px;
        }

        label {
            font-size: 12.5px;
            font-weight: 600;
            display: block;
            margin: 14px 0 6px;
        }

        input[type=text],
        input[type=email],
        input[type=password],
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter';
            color: var(--navy);
        }

        .row {
            display: flex;
            gap: 12px;
        }

        .row>div {
            flex: 1;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 16px 0;
            font-size: 13px;
            color: var(--slate);
        }

        button {
            width: 100%;
            background: var(--indigo);
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Inter';
            cursor: pointer;
            margin-top: 6px;
        }

        button:hover {
            background: var(--indigo-dk);
        }

        .alert {
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .alert-error {
            background: #FDECEC;
            color: var(--danger);
        }

        .alert-success {
            background: #EAF7EE;
            color: #16A34A;
        }

        .alert ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .footer-link {
            text-align: center;
            font-size: 12.5px;
            color: var(--slate);
            margin-top: 18px;
        }

        .footer-link a {
            color: var(--indigo);
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Buat akun baru</h1>
        <p class="sub">Isi data berikut untuk mulai bergabung dengan Study Notes Hub.</p>

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

                <div class="row">
                    <div>
                        <label>NIM</label>
                        <input type="text" name="nim" value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>Jurusan</label>
                        <select name="jurusan" required>
                            <option value="">Pilih jurusan</option>
                            <?php foreach ($daftar_jurusan as $j): ?>
                                <option value="<?= $j ?>" <?= (($_POST['jurusan'] ?? '') === $j) ? 'selected' : '' ?>><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

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
</body>

</html>