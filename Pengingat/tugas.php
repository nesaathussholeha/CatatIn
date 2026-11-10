
<?php

session_start();

date_default_timezone_set('Asia/Jakarta');

// Memanggil Controller
require_once __DIR__ . '/../app/Controllers/PengingatController.php';

// Membuat objek Controller
$controller = new PengingatController();

// Menampilkan halaman Pengingat
$controller->index();

?>