<?php
function tanggalPengingat($tanggal)
{
    return date('d M Y', strtotime($tanggal));
}
?>

<div class="halaman-pengingat">

    <div class="pengingat-heading">
        <div>
            <h1>Pengingat Tugas</h1>
            <p>Catat dan pantau tugas belajarmu agar tidak terlewat.</p>
        </div>

        <button type="button" class="tombol-tambah" id="buka-modal">
            + Tambah Tugas
        </button>
    </div>

    <div class="pengingat-filter">

        <form method="GET" action="<?= BASE_URL ?>/">
            <input type="hidden" name="page" value="pengingat">

            <select name="filter" onchange="this.form.submit()">
                <option value="semua"
                    <?= $filter === 'semua' ? 'selected' : '' ?>>
                    Semua Tugas
                </option>

                <option value="belum"
                    <?= $filter === 'belum' ? 'selected' : '' ?>>
                    Belum Selesai
                </option>

                <option value="selesai"
                    <?= $filter === 'selesai' ? 'selected' : '' ?>>
                    Selesai
                </option>
            </select>

            <select name="urut" onchange="this.form.submit()">
                <option value="terdekat"
                    <?= $urut === 'terdekat' ? 'selected' : '' ?>>
                    Deadline Terdekat
                </option>

                <option value="terjauh"
                    <?= $urut === 'terjauh' ? 'selected' : '' ?>>
                    Deadline Terjauh
                </option>
            </select>
        </form>

    </div>

    <div class="daftar-pengingat">

        <?php if (empty($tugas)): ?>

            <div class="pengingat-kosong">
                <h3>Belum ada tugas</h3>
                <p>Tugas yang sesuai dengan filter belum tersedia.</p>
            </div>

        <?php else: ?>

            <?php foreach ($tugas as $item): ?>

                <article class="kartu-pengingat">

                    <div class="pengingat-info">

                        <h3 class="<?= $item['selesai'] ? 'teks-selesai' : '' ?>">
                            <?= htmlspecialchars($item['judul']) ?>
                        </h3>

                        <p>
                            Deadline:
                            <?= tanggalPengingat($item['deadline']) ?>
                        </p>

                        <span class="status-tugas <?= $item['selesai'] ? 'status-selesai' : 'status-belum' ?>">
                            <?= $item['selesai'] ? 'Selesai' : 'Belum selesai' ?>
                        </span>

                    </div>

                    <div class="pengingat-aksi">

                        <form method="POST" action="<?= BASE_URL ?>/?page=pengingat">
                            <input type="hidden" name="aksi" value="toggle">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">

                            <button type="submit" class="tombol-status">
                                <?= $item['selesai'] ? 'Batalkan' : 'Tandai Selesai' ?>
                            </button>
                        </form>

                        <button
                            type="button"
                            class="tombol-edit"
                            data-id="<?= (int) $item['id'] ?>"
                            data-judul="<?= htmlspecialchars($item['judul'], ENT_QUOTES) ?>"
                            data-deadline="<?= htmlspecialchars($item['deadline']) ?>">
                            Edit
                        </button>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>/?page=pengingat"
                            class="form-hapus"
                            onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">

                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">

                            <button type="submit" class="tombol-hapus">
                                Hapus
                            </button>
                        </form>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>


<!-- MODAL TAMBAH DAN EDIT -->
<div class="modal-pengingat" id="modal-pengingat">

    <div class="modal-konten">

        <div class="modal-heading">
            <h2 id="judul-modal">Tambah Tugas</h2>
            <button type="button" id="tutup-modal">&times;</button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/?page=pengingat" id="form-pengingat">

            <input type="hidden" name="aksi" id="aksi" value="tambah">
            <input type="hidden" name="id" id="id-tugas">

            <label for="judul-tugas">Nama Tugas</label>
            <input
                type="text"
                name="judul"
                id="judul-tugas"
                placeholder="Masukkan nama tugas"
                required>

            <label for="deadline-tugas">Deadline</label>
            <input
                type="date"
                name="deadline"
                id="deadline-tugas"
                required>

            <div class="modal-aksi">
                <button type="button" id="batal-modal">Batal</button>
                <button type="submit" class="tombol-simpan">Simpan</button>
            </div>

        </form>

    </div>

</div>