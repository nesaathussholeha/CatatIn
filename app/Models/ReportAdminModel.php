<?php

class ReportAdminModel {
    private $db;

    public function __construct($databaseConnection = null) {
        $this->db = $databaseConnection;
    }

    // Mendapatkan seluruh data laporan
    public function getAllReports() {
        // Jika sudah tersambung ke database MySQL (PDO), gunakan query ini:
        if ($this->db) {
            $query = "SELECT l.id, c.judul AS catatan, u.nama AS pelapor, l.jenis AS alasan, 
                             l.status, DATE(l.created_at) AS tanggal, l.deskripsi 
                      FROM laporan l
                      JOIN catatan c ON l.catatan_id = c.id
                      JOIN pengguna u ON l.user_id = u.id
                      ORDER BY l.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Data dummy awal sebagai fallback
        return [
            [
                'id'       => 1,
                'catatan'  => 'Catatan UAS Basis Data',
                'pelapor'  => 'Nazma F',
                'alasan'   => 'File Rusak',
                'status'   => 'diproses',
                'tanggal'  => '2026-10-10',
                'deskripsi'=> 'File PDF tidak bisa dibuka dan menampilkan pesan error saat diunduh.'
            ],
            [
                'id'       => 2,
                'catatan'  => 'Rangkuman Statistika',
                'pelapor'  => 'Nazma F',
                'alasan'   => 'Salah Matkul',
                'status'   => 'menunggu',
                'tanggal'  => '2026-10-11',
                'deskripsi'=> 'Catatan ini seharusnya berada di mata kuliah Probabilitas, bukan Statistika.'
            ],
            [
                'id'       => 3,
                'catatan'  => 'Modul Jaringan Komputer',
                'pelapor'  => 'Nazma F',
                'alasan'   => 'Spam',
                'status'   => 'selesai',
                'tanggal'  => '2026-10-12',
                'deskripsi'=> 'Isi catatan berisi promosi yang tidak berkaitan dengan materi kuliah.'
            ],
        ];
    }

    // Update status laporan (AJAX)
    public function updateStatus($id, $status) {
        if ($this->db) {
            $query = "UPDATE laporan SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':status' => $status, ':id' => $id]);
        }
        return true;
    }

    // Hapus laporan (AJAX)
    public function deleteReport($id) {
        if ($this->db) {
            $query = "DELETE FROM laporan WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id]);
        }
        return true;
    }
}