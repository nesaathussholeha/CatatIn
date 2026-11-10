<?php

class ReportUserModel {
    private $db;

    public function __construct($databaseConnection = null) {
        $this->db = $databaseConnection;
    }

    public function createReport($data, $file) {
        $errors = [];
        $judul      = trim($data['judul'] ?? '');
        $jenis      = trim($data['jenis'] ?? '');
        $deskripsi  = trim($data['deskripsi'] ?? '');
        $catatanId  = (int)($data['catatan_id'] ?? 0);
        $jenisValid = ['File Rusak', 'Salah Mata Kuliah', 'Spam'];

        // Validasi Judul
        if ($judul === '') {
            $errors['judul'] = 'Judul laporan wajib diisi.';
        } elseif (mb_strlen($judul) > 100) {
            $errors['judul'] = 'Judul maksimal 100 karakter.';
        }

        // Validasi Jenis
        if (!in_array($jenis, $jenisValid, true)) {
            $errors['jenis'] = 'Pilih salah satu jenis laporan.';
        }

        // Validasi Deskripsi
        if (mb_strlen($deskripsi) > 500) {
            $errors['deskripsi'] = 'Deskripsi maksimal 500 karakter.';
        }

        // Validasi File Upload
        $namaFileTersimpan = null;
        if (!empty($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $ekstensiValid = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['file'] = 'Gagal mengunggah file.';
            } elseif (!in_array($ext, $ekstensiValid, true)) {
                $errors['file'] = 'Format file tidak didukung.';
            } elseif ($file['size'] > 10 * 1024 * 1024) {
                $errors['file'] = 'Ukuran file maksimal 10 MB.';
            } else {
                $folder = __DIR__ . '/../../public/uploads/laporan/';
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }
                $namaFileTersimpan = uniqid('laporan_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $folder . $namaFileTersimpan);
            }
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        // Simpan ke database jika ada koneksi
        if ($this->db) {
            $query = "INSERT INTO laporan (catatan_id, judul, jenis, deskripsi, file_path, created_at) 
                      VALUES (:catatan_id, :judul, :jenis, :deskripsi, :file_path, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':catatan_id' => $catatanId,
                ':judul'      => $judul,
                ':jenis'      => $jenis,
                ':deskripsi'  => $deskripsi,
                ':file_path'  => $namaFileTersimpan
            ]);
        }

        return [
            'success' => true,
            'message' => 'Laporan berhasil dikirim. Terima kasih atas masukan Anda!'
        ];
    }
}