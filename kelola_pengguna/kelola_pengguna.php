<?php

session_start();

if (isset($_GET['logout'])) {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params =
            session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    header('Location: login.php');
    exit;
}

function e($nilai) {

    return htmlspecialchars(
        (string)$nilai,
        ENT_QUOTES,
        'UTF-8'
    );
}

function flash($tipe, $pesan) {

    $_SESSION['flash'] = [
        'tipe' => $tipe,
        'pesan' => $pesan
    ];
}

function panjang($teks) {

    return mb_strlen(
        $teks,
        'UTF-8'
    );
}

if (
    !isset($_SESSION['admin_catatan']) ||
    ($_SESSION['admin_versi'] ?? 0) < 2
) {

    $_SESSION['admin_catatan'] = [

        [
            'id' => 1,
            'judul' => 'Ringkasan Struktur Data — Binary Tree',
            'penulis' => 'Sarah Faradila',
            'jurusan' => 'Informatika',
            'upvote' => 128,
            'status' => 'terbit',
            'alasan_laporan' => '',
            'isi' => "Binary tree adalah struktur data berbentuk pohon di mana setiap node punya maksimal dua anak: kiri dan kanan.

Traversal ada tiga: preorder (akar-kiri-kanan), inorder (kiri-akar-kanan), dan postorder (kiri-kanan-akar).

Contoh pemakaian: pencarian data (binary search tree) dan ekspresi matematika."
        ],

        [
            'id' => 2,
            'judul' => 'Rangkuman Basis Data Relasional',
            'penulis' => 'Nazma Fairuz M.',
            'jurusan' => 'Sistem Informasi',
            'upvote' => 94,
            'status' => 'terbit',
            'alasan_laporan' => '',
            'isi' => "Basis data relasional menyimpan data dalam tabel yang punya baris dan kolom.

Konsep penting: primary key, foreign key, dan normalisasi (1NF sampai 3NF) untuk mengurangi data ganda."
        ],

        [
            'id' => 3,
            'judul' => 'Catatan Kalkulus II — Integral Lipat',
            'penulis' => 'Bagas Wicaksono',
            'jurusan' => 'Teknik Informatika',
            'upvote' => 61,
            'status' => 'dilaporkan',
            'alasan_laporan' => 'Isi catatan diduga disalin dari buku tanpa mencantumkan sumber.',
            'isi' => "Integral lipat dua dipakai untuk menghitung volume di bawah permukaan z = f(x, y) pada suatu daerah D.

Langkahnya: tentukan batas x dan y, integralkan terhadap satu variabel dulu, lalu variabel lainnya."
        ],

        [
            'id' => 4,
            'judul' => 'Ringkasan Jaringan Komputer — OSI Layer',
            'penulis' => 'Dewi Anjani',
            'jurusan' => 'Informatika',
            'upvote' => 45,
            'status' => 'terbit',
            'alasan_laporan' => '',
            'isi' => "Model OSI punya 7 layer: Physical, Data Link, Network, Transport, Session, Presentation, dan Application.

Tiap layer punya tugas sendiri, misalnya Network mengurus pengalamatan IP dan routing."
        ],

    ];

    $_SESSION['admin_pengguna'] = [

        [
            'id' => 1,
            'nama' => 'Sarah Faradila',
            'nim' => '2551506...003',
            'jurusan' => 'Informatika',
            'status' => 'aktif'
        ],

        [
            'id' => 2,
            'nama' => 'Nazma Fairuz M.',
            'nim' => '2551506...001',
            'jurusan' => 'Sistem Informasi',
            'status' => 'aktif'
        ],

        [
            'id' => 3,
            'nama' => 'Bagas Wicaksono',
            'nim' => '2551506...014',
            'jurusan' => 'Teknik Informatika',
            'status' => 'diblokir'
        ],

        [
            'id' => 4,
            'nama' => 'Dewi Anjani',
            'nim' => '2551506...022',
            'jurusan' => 'Informatika',
            'status' => 'aktif'
        ],

    ];

    $_SESSION['admin_next_id'] = 5;
    $_SESSION['admin_versi'] = 2;
}

if (empty($_SESSION['csrf'])) {

    $_SESSION['csrf'] =
        bin2hex(random_bytes(32));
}

function nimSudahDipakai(
    $nim,
    $idKecuali = 0
) {

    foreach (
        $_SESSION['admin_pengguna']
        as $p
    ) {

        if (
            $p['id'] !== $idKecuali &&
            strcasecmp(
                $p['nim'],
                $nim
            ) === 0
        ) {

            return true;
        }
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $balik =
        'kelola pengguna.php';

    if (
        !hash_equals(
            $_SESSION['csrf'],
            $_POST['csrf'] ?? ''
        )
    ) {

        flash(
            'error',
            'Sesi form tidak valid. Coba ulangi lagi ya.'
        );

        header('Location: ' . $balik);
        exit;
    }

    $aksi =
        $_POST['aksi'] ?? '';

    $id =
        (int)($_POST['id'] ?? 0);

    if (
        $aksi === 'tambah' ||
        $aksi === 'edit'
    ) {

        $nama =
            trim($_POST['nama'] ?? '');

        $nim =
            trim($_POST['nim'] ?? '');

        $jurusan =
            trim($_POST['jurusan'] ?? '');

        $error = null;

        if (
            panjang($nama) < 2 ||
            panjang($nama) > 80
        ) {

            $error =
                'Nama harus 2–80 karakter.';

        } elseif (
            panjang($nim) < 5 ||
            panjang($nim) > 30
        ) {

            $error =
                'NIM harus 5–30 karakter.';

        } elseif (
            panjang($jurusan) < 2 ||
            panjang($jurusan) > 60
        ) {

            $error =
                'Jurusan harus 2–60 karakter.';

        } elseif (
            nimSudahDipakai(
                $nim,
                $aksi === 'edit'
                    ? $id
                    : 0
            )
        ) {

            $error =
                'NIM sudah dipakai pengguna lain.';
        }

        if ($error) {

            flash(
                'error',
                $error
            );

        } elseif ($aksi === 'tambah') {

            $_SESSION['admin_pengguna'][] = [

                'id' =>
                    $_SESSION['admin_next_id']++,

                'nama' =>
                    $nama,

                'nim' =>
                    $nim,

                'jurusan' =>
                    $jurusan,

                'status' =>
                    'aktif',
            ];

            flash(
                'sukses',
                'Pengguna berhasil ditambahkan.'
            );

        } else {

            $ketemu = false;

            foreach (
                $_SESSION['admin_pengguna']
                as &$p
            ) {

                if ($p['id'] === $id) {

                    $p['nama'] =
                        $nama;

                    $p['nim'] =
                        $nim;

                    $p['jurusan'] =
                        $jurusan;

                    $ketemu = true;

                    break;
                }
            }

            unset($p);

            if ($ketemu) {

                flash(
                    'sukses',
                    'Data pengguna berhasil diperbarui.'
                );

            } else {

                flash(
                    'error',
                    'Pengguna tidak ditemukan.'
                );
            }
        }

    } elseif (
        $aksi === 'blokir' ||
        $aksi === 'bukablokir'
    ) {

        $ketemu = false;

        foreach (
            $_SESSION['admin_pengguna']
            as &$p
        ) {

            if ($p['id'] === $id) {

                $p['status'] =
                    (
                        $aksi === 'blokir'
                            ? 'diblokir'
                            : 'aktif'
                    );

                $ketemu = true;

                break;
            }
        }

        unset($p);

        if ($ketemu) {

            flash(
                'sukses',
                $aksi === 'blokir'
                    ? 'Pengguna berhasil diblokir.'
                    : 'Blokir pengguna berhasil dibuka.'
            );

        } else {

            flash(
                'error',
                'Pengguna tidak ditemukan.'
            );
        }

    } elseif ($aksi === 'hapus') {

        $sebelum =
            count(
                $_SESSION['admin_pengguna']
            );

        $_SESSION['admin_pengguna'] =
            array_values(
                array_filter(
                    $_SESSION['admin_pengguna'],
                    fn($p) =>
                        $p['id'] !== $id
                )
            );

        if (
            count(
                $_SESSION['admin_pengguna']
            ) < $sebelum
        ) {

            flash(
                'sukses',
                'Pengguna berhasil dihapus.'
            );

        } else {

            flash(
                'error',
                'Pengguna tidak ditemukan.'
            );
        }
    }

    header(
        'Location: ' .
        $balik .
        (
            isset($_GET['q'])
                ? '?q=' .
                    urlencode($_GET['q'])
                : ''
        )
    );

    exit;
}

$data_pengguna =
    $_SESSION['admin_pengguna'];

$kueri_pencarian =
    trim($_GET['q'] ?? '');

if ($kueri_pencarian !== '') {

    $data_pengguna =
        array_values(
            array_filter(
                $data_pengguna,
                function ($p)
                use ($kueri_pencarian) {

                    return
                        stripos(
                            $p['nama'],
                            $kueri_pencarian
                        ) !== false
                        ||
                        stripos(
                            $p['nim'],
                            $kueri_pencarian
                        ) !== false;
                }
            )
        );
}

$data_catatan =
    $_SESSION['admin_catatan'];

$jumlah_per_penulis = [];

foreach ($data_catatan as $c) {

    $kunci =
        mb_strtolower(
            $c['penulis'],
            'UTF-8'
        );

    $jumlah_per_penulis[$kunci] =
        (
            $jumlah_per_penulis[$kunci]
            ?? 0
        ) + 1;
}

$judul_halaman =
    'Kelola Pengguna';

$admin_nama =
    $_SESSION['admin_nama'] ?? 'Admin';

$admin_inisial =
    strtoupper(
        mb_substr(
            $admin_nama,
            0,
            1,
            'UTF-8'
        )
    );

$flash =
    $_SESSION['flash'] ?? null;

unset($_SESSION['flash']);

function dataPengguna(
    $p,
    $jumlah
) {

    $p['jumlah_catatan'] =
        $jumlah[
            mb_strtolower(
                $p['nama'],
                'UTF-8'
            )
        ] ?? 0;

    return e(
        json_encode(
            $p,
            JSON_UNESCAPED_UNICODE
        )
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?= e($judul_halaman) ?> — Catatin Admin
</title>

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>

<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="kelola pengguna.css"
>

</head>

<body>

<div class="layout">

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>

<aside
    class="sidebar"
    id="sidebar"
>

<div class="sidebar-brand">

<svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    fill="none"
>

<path
    d="M12 2L3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4z"
    fill="#4C5FE0"
/>

</svg>

Admin Panel

</div>

<ul class="sidebar-nav">

<li>
<a href="dashboard.php">
Dashboard
</a>
</li>

<li>
<a href="kelola catatan.php">
Kelola Catatan
</a>
</li>

<li>
<a
    href="kelola pengguna.php"
    class="active"
>
Kelola Pengguna
</a>
</li>

<li>
<a href="?logout=1">
Keluar
</a>
</li>

</ul>

</aside>

<main class="main">

<div class="main-header">

<div class="header-left">

<button
    class="menu-toggle"
    id="menuToggle"
    aria-label="Buka menu navigasi"
>

<svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    fill="none"
    stroke="#171A3D"
    stroke-width="2"
    stroke-linecap="round"
>

<line
    x1="3"
    y1="6"
    x2="21"
    y2="6"
/>

<line
    x1="3"
    y1="12"
    x2="21"
    y2="12"
/>

<line
    x1="3"
    y1="18"
    x2="21"
    y2="18"
/>

</svg>

</button>

<h2>
<?= e($judul_halaman) ?>
</h2>

</div>

<div class="avatar">
<?= e($admin_inisial) ?>
</div>

</div>

<?php if ($flash): ?>

<div
    class="flash flash-<?= e($flash['tipe']) ?>"
    id="flashPesan"
    role="alert"
>

<span>
<?= e($flash['pesan']) ?>
</span>

<button
    type="button"
    class="flash-tutup"
    data-tutup-flash
    aria-label="Tutup pesan"
>
&times;
</button>

</div>

<?php endif; ?>

<section class="panel">

<div class="panel-head">

<h3>
Daftar pengguna
</h3>

<div class="panel-tools">

<form
    method="get"
    style="margin:0;"
>

<input
    type="text"
    name="q"
    class="search-box"
    placeholder="Cari nama atau NIM..."
    value="<?= e($kueri_pencarian) ?>"
>

</form>

<button
    type="button"
    class="btn-utama"
    data-tambah
>
+ Tambah pengguna
</button>

</div>

</div>

<div class="table-scroll">

<table>

<thead>

<tr>

<th>
Nama
</th>

<th>
NIM
</th>

<th>
Jurusan
</th>

<th class="tengah">
Status
</th>

<th class="aksi">
Aksi
</th>

</tr>

</thead>

<tbody>

<?php if (empty($data_pengguna)): ?>

<tr>

<td
    colspan="5"
    class="kosong"
>
Tidak ada pengguna yang cocok.
</td>

</tr>

<?php else: ?>

<?php foreach ($data_pengguna as $p): ?>

<tr>

<td>
<?= e($p['nama']) ?>
</td>

<td>
<?= e($p['nim']) ?>
</td>

<td>
<?= e($p['jurusan']) ?>
</td>

<td class="tengah">

<?php if (
    $p['status'] === 'aktif'
): ?>

<span class="badge badge-terbit">
Aktif
</span>

<?php else: ?>

<span class="badge badge-dilaporkan">
Diblokir
</span>

<?php endif; ?>

</td>

<td class="aksi">

<div class="dropdown">

<button
    type="button"
    class="btn-aksi"
    data-dropdown
    aria-haspopup="true"
    aria-expanded="false"
>
Aksi
<span>
&#9662;
</span>
</button>

<div
    class="dropdown-menu"
    role="menu"
>

<button
    type="button"
    class="dropdown-item"
    data-lihat
    data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>"
>
Lihat
</button>

<button
    type="button"
    class="dropdown-item"
    data-edit
    data-item="<?= dataPengguna($p, $jumlah_per_penulis) ?>"
>
Edit
</button>

<?php if (
    $p['status'] === 'aktif'
): ?>

<button
    type="button"
    class="dropdown-item item-danger"
    data-konfirmasi
    data-aksi="blokir"
    data-id="<?= $p['id'] ?>"
    data-pesan="Blokir pengguna <?= e($p['nama']) ?>?"
>
Blokir
</button>

<?php else: ?>

<button
    type="button"
    class="dropdown-item"
    data-konfirmasi
    data-aksi="bukablokir"
    data-id="<?= $p['id'] ?>"
    data-pesan="Buka blokir pengguna <?= e($p['nama']) ?>?"
>
Buka Blokir
</button>

<?php endif; ?>

<button
    type="button"
    class="dropdown-item item-danger"
    data-konfirmasi
    data-aksi="hapus"
    data-id="<?= $p['id'] ?>"
    data-pesan="Hapus pengguna <?= e($p['nama']) ?>? Tindakan ini tidak bisa dibatalkan."
>
Hapus
</button>

</div>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

</section>

</main>

</div>

<form
    method="post"
    id="formAksi"
    hidden
>

<input
    type="hidden"
    name="csrf"
    value="<?= e($_SESSION['csrf']) ?>"
>

<input
    type="hidden"
    name="entitas"
    value="pengguna"
>

<input
    type="hidden"
    name="aksi"
>

<input
    type="hidden"
    name="id"
>

</form>

<div
    class="modal-latar"
    id="modalLihat"
    aria-hidden="true"
>

<div
    class="modal"
    role="dialog"
    aria-labelledby="judulModalLihat"
>

<h3 id="judulModalLihat">
Detail
</h3>

<dl
    class="detail-list"
    id="isiDetail"
></dl>

<div class="modal-tombol">

<button
    type="button"
    class="btn-batal"
    data-tutup
>
Tutup
</button>

</div>

</div>

</div>

<div
    class="modal-latar"
    id="modalPengguna"
    aria-hidden="true"
>

<div
    class="modal"
    role="dialog"
    aria-labelledby="judulModalPengguna"
>

<h3 id="judulModalPengguna">
Tambah pengguna
</h3>

<form
    method="post"
    class="form-modal"
>

<input
    type="hidden"
    name="csrf"
    value="<?= e($_SESSION['csrf']) ?>"
>

<input
    type="hidden"
    name="entitas"
    value="pengguna"
>

<input
    type="hidden"
    name="aksi"
    value="tambah"
>

<input
    type="hidden"
    name="id"
    value=""
>

<label for="penNama">
Nama
</label>

<input
    type="text"
    id="penNama"
    name="nama"
    required
    minlength="2"
    maxlength="80"
>

<label for="penNim">
NIM
</label>

<input
    type="text"
    id="penNim"
    name="nim"
    required
    minlength="5"
    maxlength="30"
>

<label for="penJurusan">
Jurusan
</label>

<input
    type="text"
    id="penJurusan"
    name="jurusan"
    required
    minlength="2"
    maxlength="60"
    list="daftarJurusan"
>

<div class="modal-tombol">

<button
    type="button"
    class="btn-batal"
    data-tutup
>
Batal
</button>

<button
    type="submit"
    class="btn-utama"
>
Simpan
</button>

</div>

</form>

</div>

</div>

<datalist id="daftarJurusan">

<option value="Informatika">
<option value="Sistem Informasi">
<option value="Teknik Informatika">

</datalist>

<script src="kelola pengguna.js"></script>

</body>
</html>