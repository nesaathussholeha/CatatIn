<?php

$page_title  = "Detail Catatan";
$active_menu = "dashboard"; 

$user_name    = "Username";
$user_role    = "Role";
$user_initial = strtoupper(substr($user_name, 0, 1));

$catatan = [
    'id'          => 1,
    'judul'       => 'Rangkuman Struktur Data',
    'deskripsi'   => 'Ringkasan konsep array, linked list, stack, queue, tree, dan graph lengkap dengan contoh dan kompleksitas.',
    'penulis'     => 'Sf. Rangkuman',
    'penulis_role' => 'Mahasiswa Informatika',
    'tanggal'     => '8 Oktober 2025',
    'jurusan'     => 'Informatika',
    'kategori'    => 'Rangkuman',
    'mata_kuliah' => 'Struktur Data',
    'format'      => 'PDF • 6 halaman',
    'ukuran'      => '1,2 MB',
    'total_hal'   => 6,
];

$topik = [
    'Array & struktur linear',
    'Linked list',
    'Binary search tree',
    'Rekursi',
    'Sorting dan pembahasan',
];

/* Isi pratinjau halaman 2 - 6 (ringkas) */
$halaman_lain = [
    2 => ['judul' => 'Linked List',        'isi' => 'Single, double, dan circular linked list. Operasi sisip, hapus, dan telusur beserta kompleksitas waktunya.'],
    3 => ['judul' => 'Tree & BST',         'isi' => 'Konsep node, root, leaf, dan tinggi pohon. Binary Search Tree: insert, search, delete, serta traversal in-order, pre-order, post-order.'],
    4 => ['judul' => 'Graph',              'isi' => 'Representasi graph dengan adjacency matrix dan adjacency list. Penelusuran BFS dan DFS.'],
    5 => ['judul' => 'Rekursi',            'isi' => 'Base case dan recursive case. Contoh faktorial, Fibonacci, dan Tower of Hanoi.'],
    6 => ['judul' => 'Sorting & Searching', 'isi' => 'Bubble, selection, insertion, merge, dan quick sort. Perbandingan kompleksitas serta binary search.'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['aksi'] ?? '') === 'kirim-laporan') {
    header('Content-Type: application/json; charset=utf-8');

    $errors     = [];
    $judul      = trim($_POST['judul'] ?? '');
    $jenis      = trim($_POST['jenis'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $jenisValid = ['File Rusak', 'Salah Mata Kuliah', 'Spam'];

    if ($judul === '') {
        $errors['judul'] = 'Judul laporan wajib diisi.';
    } elseif (mb_strlen($judul) > 100) {
        $errors['judul'] = 'Judul maksimal 100 karakter.';
    }

    if (!in_array($jenis, $jenisValid, true)) {
        $errors['jenis'] = 'Pilih salah satu jenis laporan.';
    }

    if (mb_strlen($deskripsi) > 500) {
        $errors['deskripsi'] = 'Deskripsi maksimal 500 karakter.';
    }

    $namaFileTersimpan = null;
    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $ekstensiValid = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errors['file'] = 'Gagal mengunggah file.';
        } elseif (!in_array($ext, $ekstensiValid, true)) {
            $errors['file'] = 'Format file tidak didukung.';
        } elseif ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            $errors['file'] = 'Ukuran file maksimal 10 MB.';
        } else {
            $folder = __DIR__ . '/uploads/laporan/';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $namaFileTersimpan = uniqid('laporan_', true) . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], $folder . $namaFileTersimpan);
        }
    }

    if ($errors) {
        http_response_code(422);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Laporan berhasil dikirim. Terima kasih atas masukan Anda!',
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?> - CatatIn</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="report_user.css">
</head>

<body>

    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div>
                <!-- Header Sidebar + Tombol Close (X) -->
                <div class="sidebar-header">
                    <div class="brand">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>CatatIn</span>
                    </div>
                    <button class="close-sidebar-btn" id="closeSidebar" aria-label="Tutup Menu">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </button>
                </div>

                <ul class="nav-list">
                    <li>
                        <a href="dashboard.php" class="nav-link <?= ($active_menu == 'dashboard') ? 'active' : ''; ?>">
                            <iconify-icon icon="akar-icons:dashboard"></iconify-icon> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="unggah-catatan.php" class="nav-link <?= ($active_menu == 'unggah') ? 'active' : ''; ?>">
                            <iconify-icon icon="reicon:document-upload"></iconify-icon> Unggah Catatan
                        </a>
                    </li>
                    <li>
                        <a href="catatan-saya.php" class="nav-link <?= ($active_menu == 'catatan_saya') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:note"></iconify-icon> Catatan Saya
                        </a>
                    </li>
                    <li>
                        <a href="koleksi.php" class="nav-link <?= ($active_menu == 'koleksi') ? 'active' : ''; ?>">
                            <iconify-icon icon="codicon:folder-opened-compact"></iconify-icon> Koleksi
                        </a>
                    </li>
                    <li>
                        <a href="tugas.php" class="nav-link <?= ($active_menu == 'tugas') ? 'active' : ''; ?>">
                            <iconify-icon icon="hugeicons:sticky-note-02"></iconify-icon> Tugas
                        </a>
                    </li>
                    <li>
                        <a href="profil.php" class="nav-link <?= ($active_menu == 'profil') ? 'active' : ''; ?>">
                            <iconify-icon icon="iconamoon:profile-circle"></iconify-icon> Profil
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <a href="logout.php" class="nav-link logout">
                    <iconify-icon icon="hugeicons:logout-square-01"></iconify-icon> Keluar
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <h1 class="page-title"><?= htmlspecialchars($page_title); ?></h1>
                </div>

                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($user_name); ?></span>
                        <span class="user-role"><?= htmlspecialchars($user_role); ?></span>
                    </div>
                    <div class="avatar">
                        <?= $user_initial; ?>
                    </div>
                </div>
            </header>

            <!-- Area Konten Utama -->
            <main class="content-area">
                <div class="detail-page">

                    <a href="koleksi.php" class="back-link">
                        <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                        Kembali ke Jelajahi Catatan
                    </a>

                    <!-- Kepala catatan -->
                    <section class="note-head">
                        <div class="note-head-info">
                            <div class="tag-row">
                                <span class="tag tag-blue"><?= htmlspecialchars($catatan['jurusan']); ?></span>
                                <span class="tag tag-gray"><?= htmlspecialchars($catatan['kategori']); ?></span>
                            </div>
                            <h2 class="note-title"><?= htmlspecialchars($catatan['judul']); ?></h2>
                            <p class="note-desc"><?= htmlspecialchars($catatan['deskripsi']); ?></p>
                            <p class="note-meta">
                                oleh <?= htmlspecialchars($catatan['penulis']); ?>
                                <span class="dot">•</span>
                                <?= htmlspecialchars($catatan['tanggal']); ?>
                            </p>
                        </div>

                        <div class="note-actions">
                            <button type="button" class="btn btn-outline" id="btnLaporkan">
                                <iconify-icon icon="lucide:flag"></iconify-icon>
                                Laporkan
                            </button>
                            <button type="button" class="btn btn-primary" id="btnUnduh">
                                <iconify-icon icon="lucide:download"></iconify-icon>
                                Unduh catatan
                            </button>
                        </div>
                    </section>

                    <div class="detail-grid">

                        <!-- Kolom kiri: pratinjau -->
                        <section class="preview-card">
                            <div class="preview-label">
                                <iconify-icon icon="lucide:file-text"></iconify-icon>
                                Pratinjau catatan
                            </div>

                            <div class="paper" id="paper">

                                <!-- Halaman 1 -->
                                <div class="paper-page active" data-page="1">
                                    <span class="paper-tag">CATATAN KULIAH</span>
                                    <h3 class="paper-title"><?= htmlspecialchars($catatan['mata_kuliah']); ?></h3>
                                    <p class="paper-sub">Konsep dasar &amp; struktur data yang paling sering dipakai dalam pemrograman.</p>

                                    <div class="paper-section">
                                        <h4>01 Mengenal struktur data</h4>
                                        <p>Struktur data adalah cara menyimpan, mengatur, dan mengelola data dalam komputer agar dapat diakses dan dimodifikasi secara efisien. Pemilihan struktur yang tepat sangat memengaruhi performa program.</p>
                                    </div>

                                    <div class="paper-section">
                                        <h4>02 Array &amp; linked list</h4>
                                        <p>Array menyimpan elemen pada blok memori yang berurutan sehingga akses lewat indeks sangat cepat. Linked list menyimpan data dalam node; setiap node menunjuk ke node berikutnya.</p>
                                        <div class="paper-diagram">
                                            <span class="node">data</span><span class="arrow">→</span>
                                            <span class="node">next</span><span class="arrow">→</span>
                                            <span class="node">null</span>
                                        </div>
                                    </div>

                                    <div class="paper-section">
                                        <h4>03 Stack &amp; queue</h4>
                                        <p>Stack menggunakan prinsip Last In, First Out (LIFO), sedangkan queue menggunakan First In, First Out (FIFO).</p>

                                        <div class="table-wrap">
                                            <table class="paper-table">
                                                <thead>
                                                    <tr>
                                                        <th>Struktur</th>
                                                        <th>Karakteristik</th>
                                                        <th>Contoh penggunaan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td>Array</td><td>Indeks acak</td><td>Daftar nilai</td></tr>
                                                    <tr><td>Linked list</td><td>Node berantai</td><td>Playlist musik</td></tr>
                                                    <tr><td>Stack</td><td>LIFO (terakhir masuk)</td><td>Undo, riwayat operasi</td></tr>
                                                    <tr><td>Queue</td><td>FIFO (pertama masuk)</td><td>Antrean pemrosesan</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="paper-footer">
                                        <span>Ringkasan Struktur Data</span>
                                        <span>01</span>
                                    </div>
                                </div>

                                <!-- Halaman 2 - 6 -->
                                <?php foreach ($halaman_lain as $no => $hal): ?>
                                    <div class="paper-page" data-page="<?= $no; ?>">
                                        <span class="paper-tag">CATATAN KULIAH</span>
                                        <h3 class="paper-title"><?= htmlspecialchars($hal['judul']); ?></h3>
                                        <div class="paper-section">
                                            <h4><?= sprintf('%02d', $no); ?> <?= htmlspecialchars($hal['judul']); ?></h4>
                                            <p><?= htmlspecialchars($hal['isi']); ?></p>
                                        </div>
                                        <div class="paper-footer">
                                            <span>Ringkasan Struktur Data</span>
                                            <span><?= sprintf('%02d', $no); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>

                            <div class="pager">
                                <span class="pager-text">Halaman <span id="pageNow">1</span> dari <?= $catatan['total_hal']; ?></span>
                                <div class="pager-controls">
                                    <button type="button" class="pager-btn" id="pagePrev" aria-label="Halaman sebelumnya">
                                        <iconify-icon icon="lucide:chevron-left"></iconify-icon>
                                    </button>
                                    <?php for ($i = 1; $i <= $catatan['total_hal']; $i++): ?>
                                        <button type="button" class="pager-btn pager-num <?= $i === 1 ? 'active' : ''; ?>" data-goto="<?= $i; ?>"><?= $i; ?></button>
                                    <?php endfor; ?>
                                    <button type="button" class="pager-btn" id="pageNext" aria-label="Halaman berikutnya">
                                        <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- Kolom kanan -->
                        <aside class="side-col">

                            <div class="side-card">
                                <h5 class="side-title">Penulis</h5>
                                <div class="author">
                                    <div class="author-avatar"><?= strtoupper(substr($catatan['penulis'], 0, 1)); ?></div>
                                    <div>
                                        <div class="author-name"><?= htmlspecialchars($catatan['penulis']); ?></div>
                                        <div class="author-role"><?= htmlspecialchars($catatan['penulis_role']); ?></div>
                                    </div>
                                </div>
                                <p class="side-note">Catatan kuliah ini disusun supaya teman belajar lebih mudah memahami materi.</p>
                                <a href="profil.php" class="side-link">
                                    Lihat profil
                                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                                </a>
                            </div>

                            <div class="side-card">
                                <h5 class="side-title">Info Catatan</h5>
                                <dl class="info-list">
                                    <div><dt>Mata Kuliah</dt><dd><?= htmlspecialchars($catatan['mata_kuliah']); ?></dd></div>
                                    <div><dt>Jurusan</dt><dd><?= htmlspecialchars($catatan['jurusan']); ?></dd></div>
                                    <div><dt>Kategori</dt><dd><?= htmlspecialchars($catatan['kategori']); ?></dd></div>
                                    <div><dt>Format</dt><dd><?= htmlspecialchars($catatan['format']); ?></dd></div>
                                    <div><dt>Ukuran</dt><dd><?= htmlspecialchars($catatan['ukuran']); ?></dd></div>
                                </dl>
                                <div class="verified">
                                    <iconify-icon icon="lucide:check-circle-2"></iconify-icon>
                                    Dinilai berguna oleh teman belajar
                                </div>
                            </div>

                            <div class="side-card">
                                <h5 class="side-title">Topik Utama</h5>
                                <ul class="topic-list">
                                    <?php foreach ($topik as $t): ?>
                                        <li><?= htmlspecialchars($t); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <button type="button" class="report-inline" id="btnLaporkanBawah">
                                <iconify-icon icon="lucide:message-square-warning"></iconify-icon>
                                Ada masalah? Laporkan catatan
                            </button>
                        </aside>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal-backdrop" id="modalLaporan" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">

            <div class="modal-head">
                <div class="modal-icon">
                    <iconify-icon icon="lucide:file-text"></iconify-icon>
                </div>
                <div class="modal-head-text">
                    <h3 id="modalTitle">Buat Laporan</h3>
                    <p>Lengkapi informasi berikut untuk membuat laporan dari catatan anda</p>
                </div>
                <button type="button" class="modal-close" id="modalClose" aria-label="Tutup">
                    <iconify-icon icon="lucide:x"></iconify-icon>
                </button>
            </div>

            <form id="formLaporan" class="modal-body" novalidate>
                <input type="hidden" name="catatan_id" value="<?= (int) $catatan['id']; ?>">

                <!-- Judul -->
                <div class="field" data-field="judul">
                    <label for="inputJudul">Judul Laporan <span class="req">*</span></label>
                    <input type="text" id="inputJudul" name="judul" maxlength="100" placeholder="Contoh : Catatan UAS Basis Data" autocomplete="off">
                    <div class="field-foot">
                        <span class="error-text" data-error="judul"></span>
                        <span class="counter" id="counterJudul">0/100</span>
                    </div>
                </div>

                <!-- Jenis -->
                <div class="field" data-field="jenis">
                    <label>Jenis File <span class="req">*</span></label>
                    <div class="chip-group" id="chipGroup" role="radiogroup" aria-label="Jenis laporan">
                        <button type="button" class="chip" role="radio" aria-checked="false" data-value="File Rusak">
                            <iconify-icon icon="lucide:file-text"></iconify-icon> File Rusak
                        </button>
                        <button type="button" class="chip" role="radio" aria-checked="false" data-value="Salah Mata Kuliah">
                            <iconify-icon icon="lucide:book-open-check"></iconify-icon> Salah Mata Kuliah
                        </button>
                        <button type="button" class="chip" role="radio" aria-checked="false" data-value="Spam">
                            <iconify-icon icon="lucide:ban"></iconify-icon> Spam
                        </button>
                    </div>
                    <div class="field-foot">
                        <span class="error-text" data-error="jenis"></span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="field" data-field="deskripsi">
                    <label for="inputDeskripsi">Deskripsi / Catatan <span class="opt">(Opsional)</span></label>
                    <textarea id="inputDeskripsi" name="deskripsi" maxlength="500" rows="4" placeholder="Jelaskan secara singkat isi  laporan atau alasan pelaporan..."></textarea>
                    <div class="field-foot">
                        <span class="error-text" data-error="deskripsi"></span>
                        <span class="counter" id="counterDeskripsi">0/500</span>
                    </div>
                </div>

                <!-- Unggah file -->
                <div class="field" data-field="file">
                    <label>Unggah File <span class="opt">(Opsional)</span></label>

                    <input type="file" id="inputFile" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" hidden>

                    <div class="dropzone" id="dropzone" tabindex="0" role="button" aria-label="Unggah file">
                        <iconify-icon icon="lucide:upload-cloud" class="dz-icon"></iconify-icon>
                        <p class="dz-title">Klik untuk menggunakan file atau tarik dan lepas</p>
                        <p class="dz-sub">Format : PDF, DOC, DOCX, XLS, XLMS (maks. 10 MB)</p>
                    </div>

                    <div class="file-preview" id="filePreview" hidden>
                        <div class="file-icon"><iconify-icon icon="lucide:file-text"></iconify-icon></div>
                        <div class="file-meta">
                            <div class="file-name" id="fileName">-</div>
                            <div class="file-size" id="fileSize">-</div>
                        </div>
                        <button type="button" class="file-remove" id="fileRemove" aria-label="Hapus file">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                        </button>
                    </div>

                    <div class="field-foot">
                        <span class="error-text" data-error="file"></span>
                    </div>
                </div>

                <div class="modal-foot">
                    <button type="button" class="btn btn-outline btn-md" id="btnBatal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-md" id="btnKirim">
                        <span class="btn-spinner" aria-hidden="true"></span>
                        <span class="btn-label">Kirim Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-stack" id="toastStack" aria-live="polite"></div>

    <script src="report_user.js"></script>
</body>

</html>