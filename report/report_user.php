<?php

session_start();

$namaWeb = 'CatatIn';

$jenisValid = [
    'File Rusak',
    'Salah Mata Kuliah',
    'Spam'
];

$errors = [];

$old = [
    'judul' => '',
    'jenis' => 'File Rusak',
    'deskripsi' => ''
];

$bukaModal = true;


/* =========================
   DATA LAPORAN
========================= */

if (!isset($_SESSION['laporan'])) {
    $_SESSION['laporan'] = [
        [
            'judul' => 'Soal UAS Basis Data 2025',
            'tanggal' => '12 Mei 2025',
            'status' => 'Diproses'
        ],
        [
            'judul' => 'Modul Jaringan Komputer',
            'tanggal' => '10 Mei 2025',
            'status' => 'Selesai'
        ]
    ];
}


/* =========================
   HAPUS DATA LAPORAN LAMA
   YANG TIDAK LENGKAP
========================= */

foreach ($_SESSION['laporan'] as $key => $laporan) {

    if (
        !isset($laporan['judul']) ||
        !isset($laporan['tanggal']) ||
        !isset($laporan['status'])
    ) {
        unset($_SESSION['laporan'][$key]);
    }

}


/* Rapikan nomor index array */
$_SESSION['laporan'] = array_values($_SESSION['laporan']);


/* =========================
   PROSES KIRIM LAPORAN
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['judul'] = trim($_POST['judul'] ?? '');
    $old['jenis'] = $_POST['jenis'] ?? '';
    $old['deskripsi'] = trim($_POST['deskripsi'] ?? '');


    /* VALIDASI JUDUL */

    if ($old['judul'] === '') {

        $errors['judul'] = 'Judul laporan wajib diisi.';

    } elseif (strlen($old['judul']) > 100) {

        $errors['judul'] = 'Judul maksimal 100 karakter.';

    }


    /* VALIDASI JENIS */

    if (!in_array($old['jenis'], $jenisValid, true)) {

        $errors['jenis'] = 'Pilih jenis laporan.';

    }


    /* VALIDASI DESKRIPSI */

    if (strlen($old['deskripsi']) > 500) {

        $errors['deskripsi'] = 'Deskripsi maksimal 500 karakter.';

    }


    /* =========================
       CEK FILE
    ========================= */

    $namaFile = null;

    if (
        isset($_FILES['file']) &&
        $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {

            $errors['file'] = 'File gagal diunggah.';

        } else {

            $namaAsli = $_FILES['file']['name'];
            $ukuranFile = $_FILES['file']['size'];

            $ext = strtolower(
                pathinfo($namaAsli, PATHINFO_EXTENSION)
            );

            $formatValid = [
                'pdf',
                'doc',
                'docx',
                'xls',
                'xlsx'
            ];


            if (!in_array($ext, $formatValid, true)) {

                $errors['file'] =
                    'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.';

            } elseif ($ukuranFile > 10 * 1024 * 1024) {

                $errors['file'] =
                    'Ukuran file maksimal 10 MB.';

            } else {

                $namaFile =
                    uniqid('lap_', true) . '.' . $ext;

            }

        }
    }


    /* =========================
       SIMPAN JIKA TIDAK ADA ERROR
    ========================= */

    if (empty($errors)) {

        /* SIMPAN FILE */

        if ($namaFile !== null) {

            $dir = __DIR__ . '/uploads';

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $dir . '/' . $namaFile
            );
        }


        /* SIMPAN LAPORAN */

        array_unshift(
            $_SESSION['laporan'],
            [
                'judul' => $old['judul'],

                'tanggal' =>
                    date('j') . ' ' .
                    [
                        'Januari',
                        'Februari',
                        'Maret',
                        'April',
                        'Mei',
                        'Juni',
                        'Juli',
                        'Agustus',
                        'September',
                        'Oktober',
                        'November',
                        'Desember'
                    ][date('n') - 1] .
                    ' ' . date('Y'),

                'status' => 'Diproses'
            ]
        );


        /* PESAN BERHASIL */

        $_SESSION['flash'] =
            'Laporan berhasil dikirim.';


        /* KEMBALI KE HALAMAN LAPORAN */

        header('Location: report_user.php');
        exit;
    }


    $bukaModal = true;
}


/* =========================
   PESAN BERHASIL
========================= */

$flash = $_SESSION['flash'] ?? null;

unset($_SESSION['flash']);

if ($flash) {
    $bukaModal = false;
}


/* =========================
   FUNGSI ESCAPE
========================= */

function e($s)
{
    return htmlspecialchars(
        (string) $s,
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =========================
   MENU SIDEBAR
========================= */

$menu = [
    ['Dashboard', 'dashboard', false],
    ['Unggah Catatan', 'upload', false],
    ['Catatan Saya', 'file', false],
    ['Koleksi Belajar', 'folder', false],
    ['Tugas Belajar', 'task', false],
    ['Profil', 'user', false],
    ['Keluar', 'logout', false],
];


/* =========================
   IKON SIDEBAR
========================= */

$ikon = [
    'dashboard' =>
        '<rect x="4" y="4" width="6" height="6"/>
         <rect x="14" y="4" width="6" height="6"/>
         <rect x="4" y="14" width="6" height="6"/>
         <rect x="14" y="14" width="6" height="6"/>',

    'upload' =>
        '<path d="M12 3v12"/>
         <path d="M7 8l5-5 5 5"/>
         <path d="M5 21h14"/>',

    'file' =>
        '<path d="M6 3h9l3 3v15H6z"/>
         <path d="M14 3v4h4"/>',

    'folder' =>
        '<path d="M3 6h7l2 2h9v10H3z"/>',

    'task' =>
        '<path d="M5 4h14v16H5z"/>
         <path d="M8 9h8M8 13h8M8 17h5"/>',

    'user' =>
        '<circle cx="12" cy="8" r="4"/>
         <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',

    'logout' =>
        '<path d="M10 5H5v14h5"/>
         <path d="M14 8l4 4-4 4"/>
         <path d="M18 12H9"/>'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report User</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="report_user.css">
</head>
<body>

<aside class="sidebar">

    <div class="brand">
        <svg viewBox="0 0 24 24" class="ico">
            <rect x="7" y="7" width="10" height="10"></rect>
        </svg>
        <span><?= e($namaWeb) ?></span>
    </div>

    <nav>
        <?php foreach ($menu as [$label, $ic, $aktif]): ?>
            <a href="#" class="nav-item<?= $aktif ? ' active' : '' ?>">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>

</aside>

<main class="content">
    <header class="topbar">
        <h1>Laporan Saya</h1>
        <button class="btn btn-primary" id="btnBuka" type="button">
            <svg viewBox="0 0 24 24" class="ico"><path d="M12 5v14M5 12h14"/></svg>
            Buat Laporan
        </button>
    </header>

    <?php if ($flash): ?>
        <div class="alert" id="alert"><?= e($flash) ?></div>
    <?php endif; ?>

    <section class="card">
        <table>
            <thead>
                <tr><th>No</th><th>Judul Laporan</th><th>Tanggal Upload</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['laporan'] as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="judul"><?= e($r['judul']) ?></td>
                        <td><?= e($r['tanggal']) ?></td>
                        <td><span class="badge <?= $r['status'] === 'Selesai' ? 'selesai' : 'proses' ?>"><?= e($r['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<!-- Modal Buat Laporan -->
<div class="overlay<?= $bukaModal ? ' show' : '' ?>" id="overlay">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="close" id="btnTutup" type="button" aria-label="Tutup">
            <svg viewBox="0 0 24 24" class="ico"><path d="M5 5l14 14M19 5L5 19"/></svg>
        </button>

        <div class="modal-head">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" class="ico"><path d="M6 2h9l5 5v15H6zM14 2v6h6M9 13h6M9 17h6"/></svg>
            </div>
            <div>
                <h2 id="modalTitle">Buat Laporan</h2>
                <p>Lengkapi informasi berikut untuk membuat laporan dari catatan Anda.</p>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data" id="formLaporan" novalidate>
            <div class="field">
                <label for="judul">Judul Laporan <span class="req">*</span></label>
                <input type="text" id="judul" name="judul" maxlength="100"
                       placeholder="Contoh: Laporan UAS Basis Data 2025" value="<?= e($old['judul']) ?>"
                       class="<?= isset($errors['judul']) ? 'invalid' : '' ?>">
                <div class="meta">
                    <span class="error"><?= e($errors['judul'] ?? '') ?></span>
                    <span class="count" data-for="judul" data-max="100">0/100</span>
                </div>
            </div>

            <div class="field">
                <label>Jenis File <span class="req">*</span></label>
                <div class="chips">
                    <?php
                    $chipIkon = [
                        'File Rusak' => '<path d="M6 2h9l5 5v15H6zM14 2v6h6M9 13h6M9 17h6"/>',
                        'Salah Mata Kuliah' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
                        'Spam' => '<circle cx="12" cy="12" r="9"/><path d="M5.6 5.6l12.8 12.8"/>',
                    ];
                    foreach ($jenisValid as $j): ?>
                        <label class="chip">
                            <input type="radio" name="jenis" value="<?= e($j) ?>" <?= $old['jenis'] === $j ? 'checked' : '' ?>>
                            <span>
                                <svg viewBox="0 0 24 24" class="ico"><?= $chipIkon[$j] ?></svg>
                                <?= e($j) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($errors['jenis'])): ?><div class="meta"><span class="error"><?= e($errors['jenis']) ?></span></div><?php endif; ?>
            </div>

            <div class="field">
                <label for="deskripsi">Deskripsi / Catatan <small>(Opsional)</small></label>
                <textarea id="deskripsi" name="deskripsi" maxlength="500"
                          placeholder="Jelaskan secara singkat isi laporan atau alasan pelaporan..."><?= e($old['deskripsi']) ?></textarea>
                <div class="meta">
                    <span class="error"><?= e($errors['deskripsi'] ?? '') ?></span>
                    <span class="count" data-for="deskripsi" data-max="500">0/500</span>
                </div>
            </div>

            <div class="field">
                <label>Unggah File <small>(Opsional)</small></label>
                <div class="dropzone" id="dropzone" tabindex="0">
                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.xls,.xlsx" hidden>
                    <svg viewBox="0 0 24 24" class="ico cloud"><path d="M7 18a5 5 0 0 1-.6-9.96A6 6 0 0 1 18 9a4.5 4.5 0 0 1-.5 9M12 21V11m-4 4l4-4 4 4"/></svg>
                    <p class="dz-text" id="dzText">Klik untuk mengunggah file atau tarik dan lepas</p>
                    <p class="dz-hint">Format: PDF, DOC, DOCX, XLS, XLSX (maks. 10 MB)</p>
                </div>
                <div class="meta"><span class="error" id="fileError"><?= e($errors['file'] ?? '') ?></span></div>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-outline" id="btnBatal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

<script src="report_user.js"></script>
</body>
</html>