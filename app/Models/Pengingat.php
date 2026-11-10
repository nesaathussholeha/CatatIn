
<?php

class Pengingat
{
    public function __construct()
    {
        // Data awal jika Session belum memiliki data tugas
        if (!isset($_SESSION['tugas'])) {
            $_SESSION['tugas'] = [
                [
                    'id' => 1,
                    'judul' => 'Review Catatan Algoritma Bab 3',
                    'deadline' => '2026-10-02',
                    'selesai' => false
                ],
                [
                    'id' => 2,
                    'judul' => 'Baca Modul Basis Data Bab 5',
                    'deadline' => '2026-10-08',
                    'selesai' => false
                ],
                [
                    'id' => 3,
                    'judul' => 'Rangkum Materi Jaringan Bab 2',
                    'deadline' => '2026-09-25',
                    'selesai' => true
                ]
            ];

            $_SESSION['next_id'] = 4;
        }
    }

    // Mengambil semua data tugas
    public function getAll()
    {
        return $_SESSION['tugas'];
    }

    // Menambahkan tugas
    public function tambah($judul, $deadline)
    {
        if ($judul === '' || $deadline === '') {
            return false;
        }

        $_SESSION['tugas'][] = [
            'id' => $_SESSION['next_id']++,
            'judul' => $judul,
            'deadline' => $deadline,
            'selesai' => false
        ];

        return true;
    }

    // Mengubah tugas
    public function edit($id, $judul, $deadline)
    {
        foreach ($_SESSION['tugas'] as &$tugas) {
            if ($tugas['id'] === $id) {
                if ($judul === '' || $deadline === '') {
                    unset($tugas);
                    return false;
                }

                $tugas['judul'] = $judul;
                $tugas['deadline'] = $deadline;

                unset($tugas);
                return true;
            }
        }

        unset($tugas);
        return false;
    }

    // Menghapus tugas
    public function hapus($id)
    {
        $_SESSION['tugas'] = array_values(
            array_filter(
                $_SESSION['tugas'],
                fn($tugas) => $tugas['id'] !== $id
            )
        );
    }

    // Mengubah status selesai tugas
    public function toggle($id)
    {
        foreach ($_SESSION['tugas'] as &$tugas) {
            if ($tugas['id'] === $id) {
                $tugas['selesai'] = !$tugas['selesai'];
                break;
            }
        }

        unset($tugas);
    }

    // Filter dan mengurutkan tugas
    public function getFiltered($filter, $urut)
    {
        $daftar = array_filter(
            $this->getAll(),
            function ($tugas) use ($filter) {
                if ($filter === 'belum') {
                    return !$tugas['selesai'];
                }

                if ($filter === 'selesai') {
                    return $tugas['selesai'];
                }

                return true;
            }
        );

        $daftar = array_values($daftar);

        usort($daftar, function ($a, $b) use ($urut) {
            $hasil = strtotime($a['deadline'])
                <=> strtotime($b['deadline']);

            return $urut === 'terjauh' ? -$hasil : $hasil;
        });

        return $daftar;
    }

    // Format tanggal
    public function formatTanggal($tanggal)
    {
        $bulan = [
            '', 'Jan', 'Feb', 'Mar', 'Apr',
            'Mei', 'Jun', 'Jul', 'Agu',
            'Sep', 'Okt', 'Nov', 'Des'
        ];

        $ts = strtotime($tanggal);

        return date('j', $ts) . ' '
            . $bulan[(int) date('n', $ts)] . ' '
            . date('Y', $ts);
    }

    // Menentukan status tugas
    public function statusTugas($tugas)
    {
        if ($tugas['selesai']) {
            return ['Selesai', 'badge-selesai'];
        }

        $hariIni = strtotime(date('Y-m-d'));
        $deadline = strtotime($tugas['deadline']);
        $sisaHari = ($deadline - $hariIni) / 86400;

        if ($sisaHari < 0) {
            return ['Terlambat', 'badge-terlambat'];
        }

        if ($sisaHari <= 3) {
            return ['Mendekati deadline', 'badge-dekat'];
        }

        return ['Belum jatuh tempo', 'badge-aman'];
    }
}
