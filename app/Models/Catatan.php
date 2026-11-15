<?php

class Catatan
{
    public const VISIBILITAS = ['publik', 'pribadi'];

    private static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['catatan_data'])) {
            $seed = [
                ['id' => 1, 'judul' => 'Ringkasan Struktur Data — Binary Tree', 'penulis' => 'Sarah Faradila', 'jurusan' => 'Informatika', 'matkul' => 'Struktur Data', 'tipe' => 'Rangkuman', 'upvote' => 128, 'status' => 'Terbit', 'visibilitas' => 'publik'],
                ['id' => 2, 'judul' => 'Rangkuman Basis Data Relasional', 'penulis' => 'Nazma Fairuz M.', 'jurusan' => 'Sistem Informasi', 'matkul' => 'Basis Data', 'tipe' => 'Rangkuman', 'upvote' => 94, 'status' => 'Terbit', 'visibilitas' => 'publik'],
                ['id' => 3, 'judul' => 'Catatan Kalkulus II — Integral Lipat', 'penulis' => 'Bagas Wicaksono', 'jurusan' => 'Informatika', 'matkul' => 'Kalkulus II', 'tipe' => 'Soal Ujian', 'upvote' => 61, 'status' => 'Dilaporkan', 'visibilitas' => 'publik', 'alasan' => 'Materi perlu diperiksa kembali.'],
                ['id' => 4, 'judul' => 'Ringkasan Jaringan Komputer — OSI Layer', 'penulis' => 'Dewi Anjani', 'jurusan' => 'Informatika', 'matkul' => 'Jaringan Komputer', 'tipe' => 'Modul', 'upvote' => 45, 'status' => 'Terbit', 'visibilitas' => 'publik'],
            ];

            // Semua kunci dijamin ada, supaya View tidak kena "undefined key"
            $default = [
                'id' => 0, 'judul' => '', 'penulis' => '', 'jurusan' => '', 'matkul' => '',
                'tipe' => 'Rangkuman', 'deskripsi' => '', 'tautan' => '', 'anonim' => false,
                'visibilitas' => 'pribadi', 'upvote' => 0, 'status' => 'Draft',
                'isi' => '', 'alasan' => '',
            ];

            $_SESSION['catatan_data'] = array_map(
                static fn(array $n): array => $n + $default,
                $seed
            );
        }
    }

    public static function semua(): array
    {
        self::init();
        return $_SESSION['catatan_data'];
    }

    // Semua catatan (dipakai fitur lain / admin)
    public function ambilSemua(): array
    {
        return self::semua();
    }

    // Hanya catatan milik penulis tertentu (untuk halaman "Catatan Saya")
    public function ambilMilik(string $penulis): array
    {
        return array_values(array_filter(
            self::semua(),
            static fn(array $n): bool => $n['penulis'] === $penulis
        ));
    }

    // Catatan yang boleh dilihat umum: Terbit + publik. Penulis disembunyikan bila anonim.
    public function ambilPublik(): array
    {
        $hasil = array_filter(
            self::semua(),
            static fn(array $n): bool => $n['status'] === 'Terbit' && $n['visibilitas'] === 'publik'
        );

        return array_values(array_map(static function (array $n): array {
            if ($n['anonim']) {
                $n['penulis'] = 'Anonim';
            }
            return $n;
        }, $hasil));
    }

    public function ambilById(int $id): ?array
    {
        foreach (self::semua() as $note) {
            if ((int) $note['id'] === $id) {
                return $note;
            }
        }
        return null;
    }

    public static function tambah(array $data): void
    {
        self::init();

        $ids   = array_column($_SESSION['catatan_data'], 'id');
        $newId = empty($ids) ? 1 : max($ids) + 1;
        $visibilitas = in_array($data['visibilitas'] ?? '', self::VISIBILITAS, true)
            ? $data['visibilitas']
            : 'pribadi';

        $_SESSION['catatan_data'][] = [
            'id'          => $newId,
            'judul'       => trim($data['judul']),
            'penulis'     => trim($data['penulis'] ?? ($_SESSION['nama'] ?? 'User')),
            'jurusan'     => trim($data['jurusan']),
            'matkul'      => trim($data['matkul'] ?? ''),
            'tipe'        => trim($data['tipe'] ?? ''),
            'deskripsi'   => trim($data['deskripsi'] ?? ''),
            'tautan'      => trim($data['tautan'] ?? ''),
            'anonim'      => $visibilitas === 'publik' && ($data['anonim'] ?? false),
            'visibilitas' => $visibilitas,
            'upvote'      => 0,
            'status'      => 'Draft',
            'isi'         => trim($data['deskripsi'] ?? ''),
            'alasan'      => '',
        ];
    }

    public static function edit($id, array $data): bool
    {
        self::init();

        foreach ($_SESSION['catatan_data'] as $key => $note) {
            if ((int) $note['id'] !== (int) $id) {
                continue;
            }

            $visibilitas = in_array($data['visibilitas'] ?? '', self::VISIBILITAS, true)
                ? $data['visibilitas']
                : $note['visibilitas'];

            $_SESSION['catatan_data'][$key] = array_merge($note, [
                'judul'       => trim($data['judul'] ?? $note['judul']),
                'matkul'      => trim($data['matkul'] ?? ''),
                'jurusan'     => trim($data['jurusan'] ?? $note['jurusan']),
                'tipe'        => trim($data['tipe'] ?? ''),
                'deskripsi'   => trim($data['deskripsi'] ?? ''),
                'tautan'      => trim($data['tautan'] ?? ''),
                'visibilitas' => $visibilitas,
                'anonim'      => $visibilitas === 'publik' && ($data['anonim'] ?? false),
            ]);

            return true;
        }

        return false;
    }

    public function update(int $id, array $data): bool
    {
        return self::edit($id, $data);
    }

    public static function ubahStatus($id, $status): bool
    {
        self::init();

        if (!in_array($status, ['Terbit', 'Draft'], true)) {
            return false;
        }

        foreach ($_SESSION['catatan_data'] as $key => $note) {
            if ((int) $note['id'] === (int) $id) {
                $_SESSION['catatan_data'][$key]['status'] = $status;
                if ($status === 'Terbit') {
                    $_SESSION['catatan_data'][$key]['alasan'] = '';
                }
                return true;
            }
        }

        return false;
    }

    public static function hapus($id): bool
    {
        self::init();

        foreach ($_SESSION['catatan_data'] as $key => $note) {
            if ((int) $note['id'] === (int) $id) {
                unset($_SESSION['catatan_data'][$key]);
                $_SESSION['catatan_data'] = array_values($_SESSION['catatan_data']);
                return true;
            }
        }

        return false;
    }
}