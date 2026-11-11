<?php

class Playlist
{
    public function __construct()
    {
        if (!isset($_SESSION['playlists'])) {
            $_SESSION['playlists'] = [
                ['id' => 1, 'nama' => 'Persiapan UAS Semester 3', 'deskripsi' => 'Kumpulan rangkuman untuk UAS.', 'jumlah' => 5],
                ['id' => 2, 'nama' => 'Belajar Pemrograman Web',  'deskripsi' => 'Modul HTML, CSS, dan PHP.',   'jumlah' => 3],
            ];
        }
    }

    public function getAll()
    {
        return $_SESSION['playlists'];
    }

    public function create($nama, $deskripsi)
    {
        if ($nama === '') {
            return false;
        }

        $ids = array_column($_SESSION['playlists'], 'id');

        $_SESSION['playlists'][] = [
            'id'        => $ids ? max($ids) + 1 : 1,
            'nama'      => $nama,
            'deskripsi' => $deskripsi,
            'jumlah'    => 0,
        ];

        return true;
    }

    public function delete($id)
    {
        $id = (int) $id;

        $_SESSION['playlists'] = array_values(array_filter(
            $_SESSION['playlists'],
            function ($p) use ($id) {
                return $p['id'] !== $id;
            }
        ));
    }
}