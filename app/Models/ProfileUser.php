<?php

class ProfileUser
{
    public function getProfile($id)
    {
        if (!isset($_SESSION['dummy_profile'])) {

            $_SESSION['dummy_profile'] = [
                'id' => 1,
                'nama_lengkap' => 'Ryul',
                'nim' => '23044110001',
                'email' => 'ryul@gmail.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567890',
                'foto_profil' => null,
                'total_upvote' => 125,
                'total_catatan' => 12,
                'badge' => 'Top Contributor'
            ];
        }

        return $_SESSION['dummy_profile'];
    }

    public function updateProfile($data)
    {
        $_SESSION['dummy_profile'] = array_merge(
            $_SESSION['dummy_profile'],
            $data
        );

        return true;
    }

    public function updatePassword($password)
    {
        $_SESSION['dummy_profile']['password'] =
            password_hash($password, PASSWORD_DEFAULT);

        return true;
    }

    public function emailExists($email)
    {
        return false;
    }

    public function nimExists($nim)
    {
        return false;
    }
}