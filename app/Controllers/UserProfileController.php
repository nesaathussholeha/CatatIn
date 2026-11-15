<?php

class UserProfileController
{
    private $profile;

    public function __construct()
    {
        $this->profile = new ProfileUser();
    }

    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?page=profil-user');
            exit;
        }

        $user = $this->profile->getProfile($_SESSION['user_id']);

        if (empty($_SESSION['csrf_profile'])) {
            $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
        }

        $errors = [
            'profil' => [],
            'password' => []
        ];

        $flash = $_SESSION['flash_profile'] ?? null;
        unset($_SESSION['flash_profile']);

        $old = $user;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!hash_equals(
                $_SESSION['csrf_profile'],
                $_POST['csrf'] ?? ''
            )) {
                http_response_code(403);
                exit('Token keamanan tidak valid.');
            }

            $aksi = $_POST['aksi'] ?? '';

            // UPDATE PROFIL
            if ($aksi === 'profil') {

                $nama = trim($_POST['nama'] ?? '');
                $nim = trim($_POST['nim'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $jurusan = trim($_POST['jurusan'] ?? '');
                $no_hp = trim($_POST['no_hp'] ?? '');

                $old = array_merge($old, [
                    'nama_lengkap' => $nama,
                    'nim' => $nim,
                    'email' => $email,
                    'jurusan' => $jurusan,
                    'no_hp' => $no_hp
                ]);

                if ($nama === '' || mb_strlen($nama) < 2) {
                    $errors['profil']['nama'] =
                        'Nama minimal 2 karakter.';
                }

                if (!preg_match('/^[0-9]{5,20}$/', $nim)) {
                    $errors['profil']['nim'] =
                        'NIM harus terdiri dari 5–20 angka.';
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['profil']['email'] =
                        'Format email tidak valid.';
                }

                if (
                    $no_hp !== '' &&
                    !preg_match('/^(\+62|0)[0-9]{8,13}$/', $no_hp)
                ) {
                    $errors['profil']['no_hp'] =
                        'Format nomor HP tidak valid.';
                }

                $fotoBaru = $user['foto_profil'];

                if (
                    isset($_FILES['foto']) &&
                    $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
                ) {
                    $foto = $_FILES['foto'];

                    if ($foto['error'] !== UPLOAD_ERR_OK) {
                        $errors['profil']['foto'] =
                            'Foto gagal diunggah.';
                    } elseif ($foto['size'] > 2 * 1024 * 1024) {
                        $errors['profil']['foto'] =
                            'Ukuran foto maksimal 2 MB.';
                    } else {

                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->file($foto['tmp_name']);

                        $format = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp'
                        ];

                        if (!isset($format[$mime])) {
                            $errors['profil']['foto'] =
                                'Gunakan JPG, PNG, atau WEBP.';
                        } else {

                            $folder = ROOT_PATH . '/public/uploads/profile/';

                            if (!is_dir($folder)) {
                                mkdir($folder, 0755, true);
                            }

                            $namaFile = 'profile_' .
                                bin2hex(random_bytes(8)) .
                                '.' . $format[$mime];

                            if (move_uploaded_file(
                                $foto['tmp_name'],
                                $folder . $namaFile
                            )) {
                                $fotoBaru = 'uploads/profile/' . $namaFile;
                            } else {
                                $errors['profil']['foto'] =
                                    'Foto gagal disimpan.';
                            }
                        }
                    }
                }

                if (empty($errors['profil'])) {

                    $this->profile->updateProfile([
                        'nama_lengkap' => $nama,
                        'nim' => $nim,
                        'email' => $email,
                        'jurusan' => $jurusan,
                        'no_hp' => $no_hp,
                        'foto_profil' => $fotoBaru
                    ]);

                    $_SESSION['nama_lengkap'] = $nama;

                    $_SESSION['flash_profile'] = [
                        'ok',
                        'Profil berhasil diperbarui.'
                    ];

                    header('Location: ' . BASE_URL . '/?page=profile');
                    exit;
                }

                $flash = ['err', 'Periksa kembali data profil.'];
            }

            // UPDATE PASSWORD
            elseif ($aksi === 'password') {

                $lama = $_POST['password_lama'] ?? '';
                $baru = $_POST['password_baru'] ?? '';
                $konfirmasi = $_POST['password_konfirmasi'] ?? '';

                if (!password_verify($lama, $user['password'])) {
                    $errors['password']['lama'] =
                        'Password saat ini salah.';
                }

                if (strlen($baru) < 8) {
                    $errors['password']['baru'] =
                        'Password minimal 8 karakter.';
                }

                if ($baru === $lama) {
                    $errors['password']['baru'] =
                        'Password baru harus berbeda.';
                }

                if ($konfirmasi !== $baru) {
                    $errors['password']['konfirmasi'] =
                        'Konfirmasi password tidak sama.';
                }

                if (empty($errors['password'])) {

                    $this->profile->updatePassword($baru);

                    $_SESSION['flash_profile'] = [
                        'ok',
                        'Password berhasil diubah.'
                    ];

                    header('Location: ' . BASE_URL . '/?page=profile');
                    exit;
                }

                $flash = ['err', 'Password belum berhasil diubah.'];
            }
        }

        // DATA LAYOUT
        $page_title = 'Profil Saya';
        $user_name = $user['nama_lengkap'];
        $user_role = $user['role'];
        $active_menu = 'profile-user';

        $page_css = 'profil_user.css';
        $page_js = 'profil_user.js';

        $stats = [
            'upvote' => $user['total_upvote'],
            'catatan' => $user['total_catatan'],
            'badge' => $user['badge']
        ];

        ob_start();

        require VIEW_PATH . '/user/profil.php';

        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}
