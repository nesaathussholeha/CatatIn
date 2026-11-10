<?php

require_once ROOT_PATH . '/app/Models/ReportUserModel.php';

class ReportUserController {
    private $reportUserModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->reportUserModel = new ReportUserModel();
    }

    public function index() {
        // Handle Request POST / AJAX Kirim Laporan
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['aksi'] ?? '') === 'kirim-laporan') {
            header('Content-Type: application/json; charset=utf-8');
            
            $response = $this->reportUserModel->createReport($_POST, $_FILES['file'] ?? []);
            
            if (!$response['success']) {
                http_response_code(422);
            }
            
            echo json_encode($response);
            exit;
        }

        // Variable yang dikirim ke Layout & View
        $pageTitle   = "Detail Catatan";
        $activeMenu  = "dashboard";
        $userName    = $_SESSION['user_name'] ?? "Username";
        $userRole    = $_SESSION['role'] ?? "Role";
        $userInitial = strtoupper(substr($userName, 0, 1));

        $catatan = [
            'id'           => 1,
            'judul'        => 'Rangkuman Struktur Data',
            'deskripsi'    => 'Ringkasan konsep array, linked list, stack, queue, tree, dan graph lengkap dengan contoh dan kompleksitas.',
            'penulis'      => 'Sf. Rangkuman',
            'penulis_role' => 'Mahasiswa Informatika',
            'tanggal'      => '8 Oktober 2025',
            'jurusan'      => 'Informatika',
            'kategori'     => 'Rangkuman',
            'mata_kuliah'  => 'Struktur Data',
            'format'       => 'PDF • 6 halaman',
            'ukuran'       => '1,2 MB',
            'total_hal'    => 6,
        ];

        $topik = [
            'Array & struktur linear',
            'Linked list',
            'Binary search tree',
            'Rekursi',
            'Sorting dan pembahasan',
        ];

        $halamanLain = [
            2 => ['judul' => 'Linked List',        'isi' => 'Single, double, dan circular linked list. Operasi sisip, hapus, dan telusur beserta kompleksitas waktunya.'],
            3 => ['judul' => 'Tree & BST',         'isi' => 'Konsep node, root, leaf, dan tinggi pohon. Binary Search Tree: insert, search, delete, serta traversal in-order, pre-order, post-order.'],
            4 => ['judul' => 'Graph',              'isi' => 'Representasi graph dengan adjacency matrix dan adjacency list. Penelusuran BFS dan DFS.'],
            5 => ['judul' => 'Rekursi',            'isi' => 'Base case dan recursive case. Contoh faktorial, Fibonacci, dan Tower of Hanoi.'],
            6 => ['judul' => 'Sorting & Searching', 'isi' => 'Bubble, selection, insertion, merge, dan quick sort. Perbandingan kompleksitas serta binary search.'],
        ];

        // Path view khusus konten report user
        $contentView = ROOT_PATH . '/app/Views/user/report_user.php';

        // Panggil layout utama (yang di dalamnya memanggil sidebar & header)
        require_once ROOT_PATH . '/app/Views/layouts/main.php';
    }
}