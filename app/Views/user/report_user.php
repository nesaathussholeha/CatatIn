<div class="detail-page">

    <a href="index.php?page=dashboard" class="back-link">
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
        <!-- Kolom Kiri: Pratinjau Catatan -->
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
                <?php foreach ($halamanLain as $no => $hal): ?>
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

        <!-- Kolom Kanan: Detail Penulis & Info -->
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
                <a href="index.php?page=profil_penyusun" class="side-link">
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

<!-- Modal Laporan -->
<div class="modal-backdrop" id="modalLaporan" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-head">
            <div class="modal-icon">
                <iconify-icon icon="lucide:file-text"></iconify-icon>
            </div>
            <div class="modal-head-text">
                <h3 id="modalTitle">Buat Laporan</h3>
                <p>Lengkapi informasi berikut untuk membuat laporan dari catatan Anda</p>
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
                <textarea id="inputDeskripsi" name="deskripsi" maxlength="500" rows="4" placeholder="Jelaskan secara singkat isi laporan atau alasan pelaporan..."></textarea>
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
                    <p class="dz-sub">Format : PDF, DOC, DOCX, XLS, XLSX (maks. 10 MB)</p>
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

<!-- Toast Notification -->
<div class="toast-stack" id="toastStack" aria-live="polite"></div>