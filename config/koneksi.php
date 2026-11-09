
<?php

// KONFIGURASI DATABASE
$host = 'localhost';
$db = 'db_catatin';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Koneksi basis data gagal.');
}

// KONFIGURASI APLIKASI
define('APP_NAME', 'CatatIn');

define('ROOT_URL', '/CatatIn');
define('BASE_URL', ROOT_URL . '/public');
define('AUTH_URL', ROOT_URL . '/auth');

// PATH FOLDER
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

define('VIEW_PATH', APP_PATH . '/Views');
define('CONTROLLER_PATH', APP_PATH . '/Controllers');
define('MODEL_PATH', APP_PATH . '/Models');
