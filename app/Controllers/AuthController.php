
<?php

class AuthController
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        // Jika sudah login, arahkan sesuai role
        if (isset($_SESSION['role'])) {
            $this->redirectByRole();
        }

        // Proses form login atau register
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            if ($action === 'login') {
                $this->login();
            } elseif ($action === 'register') {
                $this->register();
            }
        }

        // Ambil pesan dari session
        $active_tab = $_SESSION['active_tab'] ?? 'masuk';
        $error_messages = $_SESSION['error_messages'] ?? [];
        $success_message = $_SESSION['success_message'] ?? null;

        // Hapus flash message setelah diambil
        unset(
            $_SESSION['active_tab'],
            $_SESSION['error_messages'],
            $_SESSION['success_message']
        );

        // Tampilkan halaman login
        require ROOT_PATH . '/app/Views/auth/login.php';
    }


    private function login()
    {
        $identifier = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->user->findByIdentifier($identifier);

        if (!$user || !password_verify($password, $user['password'])) {

            $_SESSION['error_messages'] = [
                'Email/NIM atau password yang kamu masukkan salah.'
            ];

            $_SESSION['active_tab'] = 'masuk';

            $this->redirectLogin();
        }

        if (!$user['is_active']) {

            $_SESSION['error_messages'] = [
                'Akun kamu sedang tidak aktif.'
            ];

            $_SESSION['active_tab'] = 'masuk';

            $this->redirectLogin();
        }

        // Buat session baru setelah login berhasil
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];

        // Arahkan pengguna sesuai role
        $this->redirectByRole();
    }


    private function register()
    {
        $nama = trim($_POST['nama_lengkap'] ?? '');
        $nim = trim($_POST['nim'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $konfirmasi = $_POST['konfirmasi'] ?? '';
        $consent = isset($_POST['consent']);

        $errors = [];

        // Validasi nama
        if ($nama === '') {
            $errors[] = 'Nama lengkap wajib diisi.';
        }

        // Validasi NIM
        if (!preg_match('/^[0-9]{10,20}$/', $nim)) {
            $errors[] = 'NIM harus terdiri dari 10 sampai 20 angka.';
        }

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        // Validasi password
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal terdiri dari 8 karakter.';
        }

        // Validasi konfirmasi password
        if ($password !== $konfirmasi) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        // Validasi persetujuan
        if (!$consent) {
            $errors[] = 'Kamu harus menyetujui ketentuan yang berlaku.';
        }

        // Cek apakah NIM atau email sudah terdaftar
        if (empty($errors) && $this->user->exists($nim, $email)) {
            $errors[] = 'NIM atau email sudah terdaftar.';
        }

        // Jika terdapat kesalahan
        if (!empty($errors)) {

            $_SESSION['error_messages'] = $errors;
            $_SESSION['active_tab'] = 'daftar';

            $this->redirectLogin();
        }

        // Simpan pengguna baru
        $created = $this->user->create(
            $nama,
            $nim,
            $email,
            $password
        );

        if ($created) {

            $_SESSION['success_message'] =
                'Pendaftaran berhasil! Silakan masuk menggunakan akun kamu.';

            $_SESSION['active_tab'] = 'masuk';
        } else {

            $_SESSION['error_messages'] = [
                'Pendaftaran gagal. Silakan coba kembali.'
            ];

            $_SESSION['active_tab'] = 'daftar';
        }

        $this->redirectLogin();
    }

    private function redirectByRole()
    {
        if (strtolower($_SESSION['role']) === 'admin') {
            header('Location: ' . BASE_URL . '/?page=admin-dashboard');
            exit;
        }

        header('Location: ' . BASE_URL . '/?page=dashboard');
        exit;
    }

    private function redirectLogin()
    {
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    }
}
