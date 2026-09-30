<?php

session_start();

if (isset($_GET['logout'])) {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

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

function formatAngka($n) {

    if ($n >= 1000) {

        return rtrim(
            rtrim(
                number_format(
                    $n / 1000,
                    1,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        ) . 'k';
    }

    return (string)$n;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $balik = 'kelola catatan.php';

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

    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if (
        $aksi === 'tambah' ||
        $aksi === 'edit'
    ) {

        $judul =
            trim($_POST['judul'] ?? '');

        $penulis =
            trim($_POST['penulis'] ?? '');

        $jurusan =
            trim($_POST['jurusan'] ?? '');

        $upvote =
            filter_var(
                $_POST['upvote'] ?? 0,
                FILTER_VALIDATE_INT
            );

        $status =
            $_POST['status'] ?? 'terbit';

        $isi =
            trim($_POST['isi'] ?? '');

        $alasan =
            trim($_POST['alasan_laporan'] ?? '');

        $error = null;

        if (
            panjang($judul) < 3 ||
            panjang($judul) > 150
        ) {

            $error =
                'Judul catatan harus 3–150 karakter.';

        } elseif (
            panjang($penulis) < 2 ||
            panjang($penulis) > 80
        ) {

            $error =
                'Nama penulis harus 2–80 karakter.';

        } elseif (
            panjang($jurusan) < 2 ||
            panjang($jurusan) > 60
        ) {

            $error =
                'Jurusan harus 2–60 karakter.';

        } elseif (
            $upvote === false ||
            $upvote < 0
        ) {

            $error =
                'Upvote harus berupa angka 0 atau lebih.';

        } elseif (
            !in_array(
                $status,
                ['terbit', 'dilaporkan'],
                true
            )
        ) {

            $error =
                'Status catatan tidak valid.';

        } elseif (
            panjang($isi) < 10 ||
            panjang($isi) > 5000
        ) {

            $error =
                'Isi catatan harus 10–5000 karakter.';

        } elseif (
            panjang($alasan) > 200
        ) {

            $error =
                'Alasan laporan maksimal 200 karakter.';
        }

        if ($status !== 'dilaporkan') {
            $alasan = '';
        }

        if ($error) {

            flash(
                'error',
                $error
            );

        } elseif ($aksi === 'tambah') {

            $_SESSION['admin_catatan'][] = [

                'id' =>
                    $_SESSION['admin_next_id']++,

                'judul' =>
                    $judul,

                'penulis' =>
                    $penulis,

                'jurusan' =>
                    $jurusan,

                'upvote' =>
                    $upvote,

                'status' =>
                    $status,

                'isi' =>
                    $isi,

                'alasan_laporan' =>
                    $alasan,
            ];

            flash(
                'sukses',
                'Catatan berhasil ditambahkan.'
            );

        } else {

            $ketemu = false;

            foreach (
                $_SESSION['admin_catatan']
                as &$c
            ) {

                if ($c['id'] === $id) {

                    $c['judul'] =
                        $judul;

                    $c['penulis'] =
                        $penulis;

                    $c['jurusan'] =
                        $jurusan;

                    $c['upvote'] =
                        $upvote;

                    $c['status'] =
                        $status;

                    $c['isi'] =
                        $isi;

                    $c['alasan_laporan'] =
                        $alasan;

                    $ketemu = true;

                    break;
                }
            }

            unset($c);

            if ($ketemu) {

                flash(
                    'sukses',
                    'Catatan berhasil diperbarui.'
                );

            } else {

                flash(
                    'error',
                    'Catatan tidak ditemukan.'
                );
            }
        }

    } elseif ($aksi === 'terbitkan') {

        $ketemu = false;

        foreach (
            $_SESSION['admin_catatan']
            as &$c
        ) {

            if ($c['id'] === $id) {

                $c['status'] =
                    'terbit';

                $c['alasan_laporan'] =
                    '';

                $ketemu = true;

                break;
            }
        }

        unset($c);

        if ($ketemu) {

            flash(
                'sukses',
                'Catatan dinyatakan aman dan diterbitkan kembali.'
            );

        } else {

            flash(
                'error',
                'Catatan tidak ditemukan.'
            );
        }

    } elseif ($aksi === 'hapus') {

        $sebelum =
            count($_SESSION['admin_catatan']);

        $_SESSION['admin_catatan'] =
            array_values(
                array_filter(
                    $_SESSION['admin_catatan'],
                    fn($c) =>
                        $c['id'] !== $id
                )
            );

        if (
            count($_SESSION['admin_catatan']) <
            $sebelum
        ) {

            flash(
                'sukses',
                'Catatan berhasil dihapus.'
            );

        } else {

            flash(
                'error',
                'Catatan tidak ditemukan.'
            );
        }
    }

    header(
        'Location: ' .
        $balik .
        (
            isset($_GET['q'])
                ? '?q=' . urlencode($_GET['q'])
                : ''
        )
    );

    exit;
}

$data_catatan =
    $_SESSION['admin_catatan'];

$kueri_pencarian =
    trim($_GET['q'] ?? '');

if ($kueri_pencarian !== '') {

    $data_catatan =
        array_values(
            array_filter(
                $data_catatan,
                function ($c)
                use ($kueri_pencarian) {

                    return
                        stripos(
                            $c['judul'],
                            $kueri_pencarian
                        ) !== false
                        ||
                        stripos(
                            $c['penulis'],
                            $kueri_pencarian
                        ) !== false;
                }
            )
        );
}

$judul_halaman =
    'Kelola Catatan';

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

function dataCatatan($c) {

    return e(
        json_encode(
            $c,
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
    href="kelola catatan.css"
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
<a
    href="kelola catatan.php"
    class="active"
>
Kelola Catatan
</a>
</li>

<li>
<a href="kelola pengguna.php">
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
Daftar catatan
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
    placeholder="Cari judul atau penulis..."
    value="<?= e($kueri_pencarian) ?>"
>

</form>

<button
    type="button"
    class="btn-utama"
    data-tambah
>
+ Tambah catatan
</button>

</div>

</div>

<div class="table-scroll">

<table>

<thead>

<tr>

<th>
Judul
</th>

<th>
Penulis
</th>

<th>
Jurusan
</th>

<th class="tengah">
Upvote
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

<?php if (empty($data_catatan)): ?>

<tr>

<td
    colspan="6"
    class="kosong"
>
Tidak ada catatan yang cocok.
</td>

</tr>

<?php else: ?>

<?php foreach ($data_catatan as $c): ?>

<tr>

<td>
<?= e($c['judul']) ?>
</td>

<td>
<?= e($c['penulis']) ?>
</td>

<td>
<?= e($c['jurusan']) ?>
</td>

<td class="tengah">
<?= e($c['upvote']) ?>
</td>

<td class="tengah">

<?php if (
    $c['status'] === 'terbit'
): ?>

<span class="badge badge-terbit">
Terbit
</span>

<?php else: ?>

<span class="badge badge-dilaporkan">
Dilaporkan
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
    data-item="<?= dataCatatan($c) ?>"
>
Lihat
</button>

<button
    type="button"
    class="dropdown-item"
    data-edit
    data-item="<?= dataCatatan($c) ?>"
>
Edit
</button>

<?php if (
    $c['status'] === 'dilaporkan'
): ?>

<button
    type="button"
    class="dropdown-item item-sukses"
    data-konfirmasi
    data-aksi="terbitkan"
    data-id="<?= $c['id'] ?>"
    data-pesan="Terbitkan kembali catatan &quot;<?= e($c['judul']) ?>&quot;?"
>
Terbitkan
</button>

<?php endif; ?>

<button
    type="button"
    class="dropdown-item item-danger"
    data-konfirmasi
    data-aksi="hapus"
    data-id="<?= $c['id'] ?>"
    data-pesan="Hapus catatan &quot;<?= e($c['judul']) ?>&quot;?"
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
    value="catatan"
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
    id="modalCatatan"
    aria-hidden="true"
>

<div
    class="modal"
    role="dialog"
    aria-labelledby="judulModalCatatan"
>

<h3 id="judulModalCatatan">
Tambah catatan
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
    value="catatan"
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

<label for="catJudul">
Judul
</label>

<input
    type="text"
    id="catJudul"
    name="judul"
    required
    minlength="3"
    maxlength="150"
>

<label for="catPenulis">
Penulis
</label>

<input
    type="text"
    id="catPenulis"
    name="penulis"
    required
    minlength="2"
    maxlength="80"
>

<label for="catJurusan">
Jurusan
</label>

<input
    type="text"
    id="catJurusan"
    name="jurusan"
    required
    minlength="2"
    maxlength="60"
    list="daftarJurusan"
>

<div class="form-baris">

<div>

<label for="catUpvote">
Upvote
</label>

<input
    type="number"
    id="catUpvote"
    name="upvote"
    min="0"
    required
>

</div>

<div>

<label for="catStatus">
Status
</label>

<select
    id="catStatus"
    name="status"
>

<option value="terbit">
Terbit
</option>

<option value="dilaporkan">
Dilaporkan
</option>

</select>

</div>

</div>

<label for="catAlasan">
Alasan laporan
<span class="opsional">
(op sional)
</span>
</label>

<input
    type="text"
    id="catAlasan"
    name="alasan_laporan"
    maxlength="200"
>

<label for="catIsi">
Isi catatan
</label>

<textarea
    id="catIsi"
    name="isi"
    rows="8"
    required
    minlength="10"
    maxlength="5000"
></textarea>

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

<script src="kelola catatan.js"></script>

</body>
</html>