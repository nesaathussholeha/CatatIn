
<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Mencari pengguna berdasarkan email atau NIM
    // Mencari pengguna berdasarkan email atau NIM
    public function findByIdentifier($identifier)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users
         WHERE email = :email
         OR nim = :nim"
        );

        $stmt->execute([
            'email' => $identifier,
            'nim' => $identifier
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mengecek duplikasi NIM atau email
    public function exists($nim, $email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM users
             WHERE nim = :nim OR email = :email"
        );

        $stmt->execute([
            'nim' => $nim,
            'email' => $email
        ]);

        return $stmt->fetch() !== false;
    }

    // Menambahkan pengguna baru
    public function create($nama, $nim, $email, $password)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users
            (nama_lengkap, nim, email, password, role,
             total_upvote, badge, is_active, created_at)
            VALUES
            (:nama, :nim, :email, :password,
             'mahasiswa', 0, NULL, 1, NOW())"
        );

        return $stmt->execute([
            'nama' => $nama,
            'nim' => $nim,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}
