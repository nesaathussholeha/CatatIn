<div class="dashboard-user">

    <form class="bar-cari" role="search" id="form-cari">
        <label class="kolom-cari">
            <span aria-hidden="true">🔍</span>
            <span class="sr-only">Cari catatan</span>
            <input
                type="search"
                id="cari"
                placeholder="Cari judul, matkul, atau penyusun...">
        </label>

        <label class="pilih-urut">
            <span class="sr-only">Urutkan</span>
            <select id="urut">
                <option value="terbaru">↓ Terbaru</option>
                <option value="populer">↓ Terpopuler</option>
            </select>
        </label>
    </form>

    <div class="chip-baris" aria-label="Filter catatan">

        <label class="pilih-jurusan">
            <span class="sr-only">Jurusan</span>
            <select id="jurusan">
                <option value="">Semua jurusan</option>

                <?php foreach ($daftarJurusan as $j): ?>
                    <option value="<?= htmlspecialchars($j) ?>">
                        <?= htmlspecialchars($j) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button
            type="button"
            class="chip aktif"
            data-kategori="semua"
            aria-pressed="true">
            Semua
        </button>

        <?php foreach ($daftarKategori as $k): ?>
            <button
                type="button"
                class="chip"
                data-kategori="<?= htmlspecialchars($k) ?>"
                aria-pressed="false">
                <?= htmlspecialchars($k) ?>
            </button>
        <?php endforeach; ?>

    </div>

    <div class="grid-kartu" id="grid">

        <?php foreach ($catatan as $n): ?>

            <article
                class="kartu"
                data-jurusan="<?= htmlspecialchars($n['jurusan']) ?>"
                data-kategori="<?= htmlspecialchars($n['kategori']) ?>"
                data-tanggal="<?= htmlspecialchars($n['tanggal']) ?>"
                data-cari="<?= htmlspecialchars(mb_strtolower(
                                $n['judul'] . ' ' .
                                    $n['matkul'] . ' ' .
                                    $n['penulis']
                            )) ?>">

                <span class="lencana">
                    <?= htmlspecialchars($n['jurusan']) ?>
                </span>

                <h2>
                    <?= htmlspecialchars($n['judul']) ?>
                </h2>

                <p class="meta">
                    oleh <?= htmlspecialchars($n['penulis']) ?>
                    · <?= htmlspecialchars($n['kategori']) ?>
                </p>

                <div class="kartu-bawah">

                    <button
                        type="button"
                        class="upvote"
                        data-dasar="<?= (int) $n['upvote'] ?>"
                        aria-pressed="false">

                        <span aria-hidden="true">▲</span>
                        <span class="jumlah">
                            <?= (int) $n['upvote'] ?>
                        </span>
                        upvote

                    </button>

                    <a
                        class="detail"
                        href="<?= BASE_URL ?>/?page=report">
                        Lihat detail
                    </a>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <div class="kosong" id="kosong" hidden>
        <p><strong>Tidak ada catatan yang cocok.</strong></p>
        <p>Coba kata kunci lain atau ubah filter.</p>

        <button type="button" class="tombol" id="reset">
            Reset pencarian
        </button>
    </div>

</div>