<?php

class ReportAdmin
{
    public function getAll(): array
    {
        return [
            [
                'id'        => 1,
                'catatan'   => 'Catatan UAS Basis Data',
                'pelapor'   => 'Nazma F',
                'alasan'    => 'File Rusak',
                'status'    => 'diproses',
                'tanggal'   => '2026-10-10',
                'deskripsi' => 'File PDF tidak bisa dibuka dan menampilkan pesan error saat diunduh.'
            ],
            [
                'id'        => 2,
                'catatan'   => 'Rangkuman Statistika',
                'pelapor'   => 'Nazma F',
                'alasan'    => 'Salah Matkul',
                'status'    => 'menunggu',
                'tanggal'   => '2026-10-11',
                'deskripsi' => 'Catatan ini seharusnya berada di mata kuliah Probabilitas, bukan Statistika.'
            ],
            [
                'id'        => 3,
                'catatan'   => 'Modul Jaringan Komputer',
                'pelapor'   => 'Nazma F',
                'alasan'    => 'Spam',
                'status'    => 'selesai',
                'tanggal'   => '2026-10-12',
                'deskripsi' => 'Isi catatan berisi promosi yang tidak berkaitan dengan materi kuliah.'
            ],
        ];
    }
}