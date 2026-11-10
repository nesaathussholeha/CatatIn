
<?php

class Catatan
{
    private static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['catatan_data'])) {
            $_SESSION['catatan_data'] = [
                [
                    'id' => 1,
                    'judul' => 'Ringkasan Struktur Data — Binary Tree',
                    'penulis' => 'Sarah Faradila',
                    'jurusan' => 'Informatika',
                    'upvote' => 128,
                    'status' => 'Terbit',
                    'isi' => 'Catatan mengenai struktur data Binary Tree.',
                    'alasan' => ''
                ],
                [
                    'id' => 2,
                    'judul' => 'Rangkuman Basis Data Relasional',
                    'penulis' => 'Nazma Fairuz M.',
                    'jurusan' => 'Sistem Informasi',
                    'upvote' => 94,
                    'status' => 'Terbit',
                    'isi' => 'Rangkuman mengenai basis data relasional.',
                    'alasan' => ''
                ],
                [
                    'id' => 3,
                    'judul' => 'Catatan Kalkulus II — Integral Lipat',
                    'penulis' => 'Bagas Wicaksono',
                    'jurusan' => 'Teknik Informatika',
                    'upvote' => 61,
                    'status' => 'Dilaporkan',
                    'isi' => 'Pembahasan integral lipat.',
                    'alasan' => 'Materi perlu diperiksa kembali.'
                ],
                [
                    'id' => 4,
                    'judul' => 'Ringkasan Jaringan Komputer — OSI Layer',
                    'penulis' => 'Dewi Anjani',
                    'jurusan' => 'Informatika',
                    'upvote' => 45,
                    'status' => 'Terbit',
                    'isi' => 'Ringkasan mengenai OSI Layer.',
                    'alasan' => ''
                ]
            ];
        }
    }

    public static function semua()
    {
        self::init();

        return $_SESSION['catatan_data'];
    }

    public static function tambah($data)
    {
        self::init();

        $ids = array_column($_SESSION['catatan_data'], 'id');

        $newId = empty($ids) ? 1 : max($ids) + 1;

        $_SESSION['catatan_data'][] = [
            'id' => $newId,
            'judul' => trim($data['judul']),
            'penulis' => trim($data['penulis']),
            'jurusan' => trim($data['jurusan']),
            'upvote' => 0,
            'status' => 'Draft',
            'isi' => trim($data['isi']),
            'alasan' => ''
        ];
    }

    public static function edit($id, $data)
    {
        self::init();

        foreach ($_SESSION['catatan_data'] as $key => $note) {
            if ((int) $note['id'] === (int) $id) {
                $_SESSION['catatan_data'][$key]['judul'] =
                    trim($data['judul']);

                $_SESSION['catatan_data'][$key]['penulis'] =
                    trim($data['penulis']);

                $_SESSION['catatan_data'][$key]['jurusan'] =
                    trim($data['jurusan']);

                $_SESSION['catatan_data'][$key]['isi'] =
                    trim($data['isi']);

                return true;
            }
        }

        return false;
    }

    public static function ubahStatus($id, $status)
    {
        self::init();

        $statusValid = ['Terbit', 'Draft'];

        if (!in_array($status, $statusValid, true)) {
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

    public static function hapus($id)
    {
        self::init();

        foreach ($_SESSION['catatan_data'] as $key => $note) {
            if ((int) $note['id'] === (int) $id) {
                unset($_SESSION['catatan_data'][$key]);

                $_SESSION['catatan_data'] = array_values(
                    $_SESSION['catatan_data']
                );

                return true;
            }
        }

        return false;
    }
}
