
<?php
$penulis = $penulis ?? [];
$catatan = $catatan ?? [];

$namaPenulis = $penulis['nama'] ?? 'Nama Lengkap';
$inisial = strtoupper(substr($namaPenulis, 0, 2));
$kategoriList = array_unique(array_filter(array_column($catatan, 'kategori')));
?>

<div class="profile-penulis">

    <!-- Breadcrumb -->
    <div class="profile-breadcrumb">
        <a href="<?= BASE_URL ?>/?page=detail-catatan">
            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
            Kembali ke Detail Catatan
        </a>
        <span>/</span>
        <span class="breadcrumb-current">Ringkuman Struktur Data</span>
    </div>

    <!-- Informasi Profil -->
    <section class="profile-card">

        <div class="profile-main">
            <div class="profile-avatar">
                <?php if (!empty($penulis['foto'])): ?>
                    <img
                        src="<?= htmlspecialchars($penulis['foto']) ?>"
                        alt="Foto <?= htmlspecialchars($namaPenulis) ?>"
                    >
                <?php else: ?>
                    <span><?= htmlspecialchars($inisial) ?></span>
                <?php endif; ?>
            </div>

            <div class="profile-identity">
                <h2><?= htmlspecialchars($namaPenulis) ?></h2>

                <p class="profile-major">
                    <?= htmlspecialchars($penulis['jurusan'] ?? 'Mahasiswa') ?>
                </p>

                <p class="profile-joined">
                    <iconify-icon icon="lucide:calendar-days"></iconify-icon>
                    Bergabung sejak
                    <?= htmlspecialchars($penulis['bergabung'] ?? 'September 2026') ?>
                </p>
            </div>

            <button type="button" class="btn-report" onclick="toggleModal(true)">
                <iconify-icon icon="lucide:flag"></iconify-icon>
                Laporkan pengguna
            </button>
        </div>

        <!-- Statistik -->
        <div class="profile-statistics">

            <div class="profile-stat">
                <div class="stat-icon">
                    <iconify-icon icon="lucide:file-text"></iconify-icon>
                </div>
                <div>
                    <h3><?= (int) ($penulis['jumlah_catatan'] ?? count($catatan)) ?></h3>
                    <p>Catatan diunggah</p>
                </div>
            </div>

            <div class="profile-stat">
                <div class="stat-icon">
                    <iconify-icon icon="lucide:thumbs-up"></iconify-icon>
                </div>
                <div>
                    <h3 id="totalUpvote">
                        <?= number_format((int) ($penulis['jumlah_upvote'] ?? 0), 0, ',', '.') ?>
                    </h3>
                    <p>Total upvote</p>
                </div>
            </div>

            <div class="profile-stat">
                <div class="stat-icon">
                    <iconify-icon icon="lucide:download"></iconify-icon>
                </div>
                <div>
                    <h3>
                        <?= number_format((int) ($penulis['jumlah_unduhan'] ?? 0), 0, ',', '.') ?>
                    </h3>
                    <p>Total unduhan</p>
                </div>
            </div>

            <div class="profile-stat">
                <div class="stat-icon">
                    <iconify-icon icon="lucide:book-open"></iconify-icon>
                </div>
                <div>
                    <h3><?= (int) ($penulis['jumlah_matkul'] ?? 0) ?></h3>
                    <p>Mata kuliah</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Daftar Catatan -->
    <section class="uploaded-section">

        <div class="uploaded-heading">
            <div>
                <div class="heading-title">
                    <h2>Catatan yang diunggah</h2>
                    <span class="total-badge"><?= count($catatan) ?> catatan</span>
                </div>
                <p>Jelajahi rangkuman, latihan soal, dan catatan kuliah dari <?= htmlspecialchars($inisial) ?>.</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-toolbar">

            <div class="search-box">
                <iconify-icon icon="lucide:search"></iconify-icon>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari judul atau mata kuliah..."
                >
            </div>

            <div class="filter-select">
                <iconify-icon icon="lucide:list-filter"></iconify-icon>
                <select id="categorySelect">
                    <option value="all">Semua kategori</option>

                    <?php foreach ($kategoriList as $kategori): ?>
                        <option value="<?= htmlspecialchars($kategori) ?>">
                            <?= htmlspecialchars($kategori) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-select sort-select">
                <iconify-icon icon="lucide:arrow-down-wide-narrow"></iconify-icon>
                <select id="sortSelect">
                    <option value="terbaru">Terbaru</option>
                    <option value="terlama">Terlama</option>
                    <option value="upvote">Upvote terbanyak</option>
                </select>
            </div>

        </div>

        <!-- Kumpulan Kartu -->
        <div class="note-grid" id="noteGrid">

            <?php foreach ($catatan as $item): ?>
                <?php
                    $tanggalTimestamp = !empty($item['tanggal'])
                        ? strtotime($item['tanggal'])
                        : 0;

                    $jumlahUpvote = (int) ($item['upvote'] ?? 0);
                    $judul = $item['judul'] ?? 'Tanpa Judul';
                    $kategori = $item['kategori'] ?? 'Umum';
                ?>

                <article
                    class="note-card"
                    data-title="<?= htmlspecialchars(strtolower($judul)) ?>"
                    data-category="<?= htmlspecialchars($kategori) ?>"
                    data-date="<?= $tanggalTimestamp ?>"
                    data-upvote="<?= $jumlahUpvote ?>"
                >

                    <!-- Preview Dokumen -->
                    <div class="note-preview">
                        <?php if (!empty($item['thumbnail'])): ?>
                            <img
                                src="<?= htmlspecialchars($item['thumbnail']) ?>"
                                alt="Preview <?= htmlspecialchars($judul) ?>"
                            >
                        <?php else: ?>
                            <div class="document-preview">
                                <span class="document-type">
                                    <?= htmlspecialchars($item['tipe'] ?? 'CATATAN KULIAH') ?>
                                </span>

                                <div class="document-title">
                                    <?= htmlspecialchars($judul) ?>
                                </div>

                                <div class="document-lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>

                                <div class="document-pages">
                                    <?= (int) ($item['jumlah_halaman'] ?? 7) ?> Halaman
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informasi Catatan -->
                    <div class="note-information">

                        <div class="note-tags">
                            <span class="note-tag category-tag">
                                <?= htmlspecialchars($kategori) ?>
                            </span>

                            <span class="note-tag">
                                <?= htmlspecialchars($item['tipe'] ?? 'Rangkuman') ?>
                            </span>
                        </div>

                        <h3><?= htmlspecialchars($judul) ?></h3>

                        <p class="note-description">
                            <?= htmlspecialchars($item['deskripsi'] ?? 'Tidak ada deskripsi.') ?>
                        </p>

                        <div class="note-metadata">
                            <span>
                                PDF
                                <span>•</span>
                                <?= (int) ($item['jumlah_halaman'] ?? 7) ?> halaman
                                <span>•</span>
                                <?= htmlspecialchars($item['ukuran'] ?? '1 MB') ?>
                            </span>
                        </div>

                        <span class="note-date">
                            <?= !empty($item['tanggal'])
                                ? date('d F Y', $tanggalTimestamp)
                                : 'Tanggal belum tersedia' ?>
                        </span>

                    </div>

                    <!-- Aksi Catatan -->
                    <div class="note-footer">

                        <button
                            type="button"
                            class="upvote-button"
                            onclick="toggleUpvote(this)"
                        >
                            <iconify-icon icon="lucide:thumbs-up"></iconify-icon>
                            <span class="upvote-count"><?= $jumlahUpvote ?></span>
                            <span>Upvote</span>
                        </button>

                        <a
                            href="<?= BASE_URL ?>/?page=detail-catatan&id=<?= (int) ($item['id'] ?? 0) ?>"
                            class="note-detail-link"
                        >
                            Lihat catatan
                            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

        <div class="empty-message" id="emptyMessage" hidden>
            <iconify-icon icon="lucide:search-x"></iconify-icon>
            <p>Tidak ada catatan yang cocok.</p>
        </div>

        <div class="uploaded-footer">
            <span id="displayCount">
                Menampilkan <?= count($catatan) ?> dari <?= count($catatan) ?> catatan
            </span>

            <span>Semua unggahan sudah ditampilkan</span>
        </div>

    </section>

    <!-- Modal Laporan -->
    <div class="report-modal" id="reportModal">
        <div class="report-modal-content">

            <button
                type="button"
                class="modal-close"
                onclick="toggleModal(false)"
            >
                <iconify-icon icon="lucide:x"></iconify-icon>
            </button>

            <div class="modal-icon">
                <iconify-icon icon="lucide:flag"></iconify-icon>
            </div>

            <h3>Laporkan Pengguna</h3>
            <p>
                Bantu kami menjaga lingkungan CatatIn tetap aman.
                Jelaskan alasan kamu melaporkan pengguna ini.
            </p>

            <textarea
                id="reportReason"
                placeholder="Tuliskan alasan laporan..."
            ></textarea>

            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="toggleModal(false)">
                    Batal
                </button>

                <button type="button" class="btn-submit" onclick="submitReport()">
                    Kirim Laporan
                </button>
            </div>

        </div>
    </div>

</div>
