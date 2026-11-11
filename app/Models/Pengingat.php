<?php

class Pengingat
{
    private static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        }

        if (!isset($_SESSION['next_id'])) {
            $_SESSION['next_id'] = 4;
        }
    }

    public static function semua()
    {
        self::init();
        return $_SESSION['tugas'];
    }

    public static function tambah($judul, $deadline)
    {
        self::init();

        $_SESSION['tugas'][] = [
            'id' => $_SESSION['next_id']++,
            'judul' => trim($judul),
            'deadline' => $deadline,
            'selesai' => false
        ];
    }

    public static function edit($id, $judul, $deadline)
    {
        self::init();

        foreach ($_SESSION['tugas'] as &$tugas) {
            if ($tugas['id'] == $id) {
                $tugas['judul'] = trim($judul);
                $tugas['deadline'] = $deadline;
                break;
            }
        }

        unset($tugas);
    }

    public static function hapus($id)
    {
        self::init();

        $_SESSION['tugas'] = array_values(
            array_filter(
                $_SESSION['tugas'],
                fn($tugas) => $tugas['id'] != $id
            )
        );
    }

    public static function ubahStatus($id)
    {
        self::init();

        foreach ($_SESSION['tugas'] as &$tugas) {
            if ($tugas['id'] == $id) {
                $tugas['selesai'] = !$tugas['selesai'];
                break;
            }
        }

        unset($tugas);
    }
}