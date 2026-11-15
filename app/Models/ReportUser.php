<?php

class ReportUser
{
    private array $jenisValid    = ['File Rusak', 'Salah Mata Kuliah', 'Spam'];
    private array $ekstensiValid = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    private int   $maxFile       = 10 * 1024 * 1024; // 10 MB

    /* ---------- DATA CATATAN (sementara dummy, nanti ganti query DB) ---------- */

    public function findCatatan(int $id): ?array
    {
        // TODO: ganti dengan query ke database berdasarkan $id
        return [
            'id'           => $id,
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
    }

    public function getTopik(int $id): array
    {
        return [
            'Array & struktur linear',
            'Linked list',
            'Binary search tree',
            'Rekursi',
            'Sorting dan pembahasan',
        ];
    }

    public function getHalamanLain(int $id): array
    {
        return [
            2 => ['judul' => 'Linked List',         'isi' => 'Single, double, dan circular linked list. Operasi sisip, hapus, dan telusur beserta kompleksitas waktunya.'],
            3 => ['judul' => 'Tree & BST',          'isi' => 'Konsep node, root, leaf, dan tinggi pohon. Binary Search Tree: insert, search, delete, serta traversal in-order, pre-order, post-order.'],
            4 => ['judul' => 'Graph',               'isi' => 'Representasi graph dengan adjacency matrix dan adjacency list. Penelusuran BFS dan DFS.'],
            5 => ['judul' => 'Rekursi',             'isi' => 'Base case dan recursive case. Contoh faktorial, Fibonacci, dan Tower of Hanoi.'],
            6 => ['judul' => 'Sorting & Searching', 'isi' => 'Bubble, selection, insertion, merge, dan quick sort. Perbandingan kompleksitas serta binary search.'],
        ];
    }

    /* ---------- VALIDASI LAPORAN ---------- */

    public function validasi(array $input, ?array $file): array
    {
        $errors    = [];
        $judul     = trim($input['judul'] ?? '');
        $jenis     = trim($input['jenis'] ?? '');
        $deskripsi = trim($input['deskripsi'] ?? '');

        if ($judul === '') {
            $errors['judul'] = 'Judul laporan wajib diisi.';
        } elseif (mb_strlen($judul) > 100) {
            $errors['judul'] = 'Judul maksimal 100 karakter.';
        }

        if (!in_array($jenis, $this->jenisValid, true)) {
            $errors['jenis'] = 'Pilih salah satu jenis laporan.';
        }

        if (mb_strlen($deskripsi) > 500) {
            $errors['deskripsi'] = 'Deskripsi maksimal 500 karakter.';
        }

        if ($file && !empty($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['file'] = 'Gagal mengunggah file.';
            } elseif (!in_array($ext, $this->ekstensiValid, true)) {
                $errors['file'] = 'Format file tidak didukung.';
            } elseif ($file['size'] > $this->maxFile) {
                $errors['file'] = 'Ukuran file maksimal 10 MB.';
            }
        }

        return $errors;
    }

    /* ---------- SIMPAN LAPORAN ---------- */

    public function simpan(array $data): bool
    {
        // TODO: INSERT ke tabel laporan
        // $data: catatan_id, user_id, judul, jenis, deskripsi, file (nama file / null)
        return true;
    }
}