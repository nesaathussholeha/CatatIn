<?php
$host    = 'localhost';
$db      = 'db_catatin'; // Sesuaikan dengan nama database projek kelompokmu
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage()); // Mencatat error ke log server[cite: 20]
    die('Koneksi basis data gagal.'); // Pesan aman untuk pengguna[cite: 20]
}
