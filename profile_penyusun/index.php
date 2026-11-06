<?php
session_start();


if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'mahasiswa') {
    header("Location: login.php"); 
    exit();
}

$page_title  = "Profil Penyusun";
$active_menu = "dashboard"; 

$user_name   = $_SESSION['nama'] ?? "Username"; 
$user_role   = $_SESSION['role'] ?? "Role";   

ob_start();
?>


<div class="breadcrumb">
    <a href="#"><iconify-icon icon="lucide:arrow-left"></iconify-icon> Kembali ke Detail Catatan</a>
    <span>/</span>
    <span class="active-breadcrumb">Rangkuman Struktur Data</span>
</div>

<!-- CARD PROFILE USER -->
<div class="profile-card">
    <div class="profile-header">
        <div class="profile-user-info">
            <div class="profile-avatar">SF</div>
            <div class="profile-details">
                <h2>Nama Lengkap</h2>
                <p class="role-text">Mahasiswa Informatika</p>
                <p class="join-date"><iconify-icon icon="lucide:calendar"></iconify-icon> Bergabung sejak September 2026</p>
            </div>
        </div>
        <button class="btn-report" onclick="toggleModal(true)">
            <iconify-icon icon="lucide:flag"></iconify-icon> Laporkan pengguna
        </button>
    </div>

    <!-- Stats Banner -->
    <div class="profile-stats">
        <div class="stat-item">
            <div class="stat-icon"><iconify-icon icon="lucide:file-text"></iconify-icon></div>
            <div class="stat-info">
                <span class="stat-number">6</span>
                <span class="stat-label">Catatan diunggah</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><iconify-icon icon="lucide:thumbs-up"></iconify-icon></div>
            <div class="stat-info">
                <span class="stat-number">486</span>
                <span class="stat-label">Total upvote</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><iconify-icon icon="lucide:download"></iconify-icon></div>
            <div class="stat-info">
                <span class="stat-number">1.240</span>
                <span class="stat-label">Total unduhan</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><iconify-icon icon="lucide:book-open"></iconify-icon></div>
            <div class="stat-info">
                <span class="stat-number">4</span>
                <span class="stat-label">Mata kuliah</span>
            </div>
        </div>
    </div>
</div>

<!-- SECTION HEADER & FILTERS -->
<div class="section-header">
    <div class="section-title">
        <h3>Catatan yang diunggah</h3>
        <span class="badge-count">6 catatan</span>
    </div>
    <p class="section-subtitle">Jelajahi rangkuman, latihan soal, dan catatan kuliah dari SF. Rangkuman.</p>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <div class="search-box">
            <iconify-icon icon="lucide:search" class="search-icon"></iconify-icon>
            <input type="text" id="searchInput" placeholder="Cari judul atau mata kuliah...">
        </div>
        <div class="filter-dropdowns">
            <div class="select-wrapper">
                <select id="categorySelect">
                    <option value="all">Semua kategori</option>
                    <option value="Struktur Data">Struktur Data</option>
                    <option value="Algoritma">Algoritma</option>
                    <option value="Basis Data">Basis Data</option>
                    <option value="Jaringan Komputer">Jaringan Komputer</option>
                </select>
                <iconify-icon icon="lucide:chevron-down" class="dropdown-icon"></iconify-icon>
            </div>
            <div class="select-wrapper">
                <select id="sortSelect">
                    <option value="terbaru">Terbaru</option>
                    <option value="terpopuler">Terpopuler</option>
                </select>
                <iconify-icon icon="lucide:chevron-down" class="dropdown-icon"></iconify-icon>
            </div>
        </div>
    </div>
</div>

<!-- CARDS GRID -->
<div id="notesGrid" class="notes-grid">

    <!-- CARD 1 -->
    <div class="note-card" data-title="rangkuman struktur data" data-category="Struktur Data">
        <div class="card-body">
            <div class="doc-thumbnail bg-blue">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Array & linked list</h4>
                </div>
                <div class="doc-subtext">
                    <p>Array • Stack</p>
                    <p>Queue • Linked List</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Struktur Data</span>
                        <span class="tag-secondary">Rangkuman</span>
                    </div>
                    <h3 class="doc-main-title">Rangkuman Struktur Data</h3>
                    <p class="doc-description">Konsep array, linked list, stack, dan queue.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 6 halaman • 1,2 MB</p>
                    <p>8 Oktober 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">128</span> Upvote
            </button>
            <a href="../report/report_user.php" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

    <!-- CARD 2 -->
    <div class="note-card" data-title="dasar algoritma & pemrograman" data-category="Algoritma">
        <div class="card-body">
            <div class="doc-thumbnail bg-indigo">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Logika & algoritma</h4>
                </div>
                <div class="doc-subtext">
                    <p>Percabangan • Kondisi</p>
                    <p>Loop • Fungsi</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Algoritma</span>
                        <span class="tag-secondary">Rangkuman</span>
                    </div>
                    <h3 class="doc-main-title">Dasar Algoritma & Pemrograman</h3>
                    <p class="doc-description">Percabangan, perulangan, dan fungsi dasar.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 8 halaman • 1,8 MB</p>
                    <p>6 Oktober 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">96</span> Upvote
            </button>
            <a href="#" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

    <!-- CARD 3 -->
    <div class="note-card" data-title="basis data: erd & normalisasi" data-category="Basis Data">
        <div class="card-body">
            <div class="doc-thumbnail bg-purple">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Relasi & normalisasi</h4>
                </div>
                <div class="doc-subtext">
                    <p>Entitas • Relasi</p>
                    <p>1NF • 2NF • 3NF</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Basis Data</span>
                        <span class="tag-secondary">Rangkuman</span>
                    </div>
                    <h3 class="doc-main-title">Basis Data: ERD & Normalisasi</h3>
                    <p class="doc-description">Relasi entitas dan normalisasi hingga 3NF.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 7 halaman • 1,5 MB</p>
                    <p>3 Oktober 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">84</span> Upvote
            </button>
            <a href="#" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

    <!-- CARD 4 -->
    <div class="note-card" data-title="latihan soal struktur data" data-category="Struktur Data">
        <div class="card-body">
            <div class="doc-thumbnail bg-teal">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Soal & pembahasan</h4>
                </div>
                <div class="doc-subtext">
                    <p>Push • Pop</p>
                    <p>Enqueue • Dequeue</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Struktur Data</span>
                        <span class="tag-secondary">Latihan Soal</span>
                    </div>
                    <h3 class="doc-main-title">Latihan Soal Struktur Data</h3>
                    <p class="doc-description">Soal stack dan queue dengan pembahasan.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 5 halaman • 980 KB</p>
                    <p>29 September 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">72</span> Upvote
            </button>
            <a href="#" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

    <!-- CARD 5 -->
    <div class="note-card" data-title="pengantar jaringan komputer" data-category="Jaringan Komputer">
        <div class="card-body">
            <div class="doc-thumbnail bg-sky">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Model OSI & TCP/IP</h4>
                </div>
                <div class="doc-subtext">
                    <p>Layer • Protokol</p>
                    <p>IP • Subnet</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Jaringan Komputer</span>
                        <span class="tag-secondary">Rangkuman</span>
                    </div>
                    <h3 class="doc-main-title">Pengantar Jaringan Komputer</h3>
                    <p class="doc-description">Model OSI, TCP/IP, dan pengalamatan IP.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 9 halaman • 2,1 MB</p>
                    <p>24 September 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">61</span> Upvote
            </button>
            <a href="#" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

    <!-- CARD 6 -->
    <div class="note-card" data-title="sql dasar & contoh query" data-category="Basis Data">
        <div class="card-body">
            <div class="doc-thumbnail bg-emerald">
                <div>
                    <span class="doc-tag">CATATAN KULIAH</span>
                    <h4 class="doc-title">Belajar query SQL</h4>
                </div>
                <div class="doc-subtext">
                    <p>SELECT • JOIN</p>
                    <p>WHERE • GROUP BY</p>
                    <div class="doc-pages">
                        <span>01</span><span>02</span><span>03</span>
                    </div>
                </div>
                <span class="doc-author">SF. Rangkuman</span>
            </div>
            <div class="doc-details">
                <div>
                    <div class="tag-group">
                        <span class="tag-primary">Basis Data</span>
                        <span class="tag-secondary">Catatan Kuliah</span>
                    </div>
                    <h3 class="doc-main-title">SQL Dasar & Contoh Query</h3>
                    <p class="doc-description">SELECT, JOIN, dan agregasi dengan contoh.</p>
                </div>
                <div class="doc-meta">
                    <p>PDF • 6 halaman • 1,1 MB</p>
                    <p>18 September 2026</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn-upvote" onclick="toggleUpvote(this)">
                <iconify-icon icon="lucide:thumbs-up"></iconify-icon> <span class="upvote-count">45</span> Upvote
            </button>
            <a href="#" class="btn-link">Lihat catatan <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
        </div>
    </div>

</div>

<!-- FOOTER INFO -->
<div class="footer-info">
    <p>Menampilkan 6 dari 6 catatan</p>
    <p>Semua unggahan sudah ditampilkan</p>
</div>

<!-- MODAL LAPORKAN PENGGUNA -->
<div id="reportModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Laporkan Pengguna</h3>
            <button onclick="toggleModal(false)" class="modal-close"><iconify-icon icon="lucide:x"></iconify-icon></button>
        </div>
        <p class="modal-subtext">Pilih alasan Anda melaporkan akun ini ke tim moderator.</p>
        <div class="modal-options">
            <label class="radio-option">
                <input type="radio" name="report_reason" checked>
                <span>Konten/Catatan tidak sesuai</span>
            </label>
            <label class="radio-option">
                <input type="radio" name="report_reason">
                <span>Spam atau akun palsu</span>
            </label>
            <label class="radio-option">
                <input type="radio" name="report_reason">
                <span>Pelanggaran Hak Cipta</span>
            </label>
        </div>
        <div class="modal-actions">
            <button onclick="toggleModal(false)" class="btn-cancel">Batal</button>
            <button onclick="submitReport()" class="btn-submit">Kirim Laporan</button>
        </div>
    </div>
</div>

<?php
// 6. Masukkan konten ke variabel $content lalu panggil layout utama
$content = ob_get_clean();
include('../layout_user/index.php');
?>