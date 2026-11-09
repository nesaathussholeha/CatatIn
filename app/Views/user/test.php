
<?php
$user_name = $user_name ?? 'Nesa';
$user_role = $user_role ?? 'User';
?>

<h2>Halaman Percobaan User</h2>

<p>Jika tulisan ini muncul, berarti layout User berhasil dipanggil!</p>

<div>
    <h3>Informasi Pengguna</h3>

    <p>
        Nama: <?= htmlspecialchars($user_name) ?>
    </p>

    <p>
        Role: <?= htmlspecialchars($user_role) ?>
    </p>
</div>
