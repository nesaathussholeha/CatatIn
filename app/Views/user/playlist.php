<?php
$playlists = $playlists ?? [];
?>

<div class="pl-page">

    <div class="pl-header">
        <div>
            <h1>Playlist Saya</h1>
            <p>Kelompokkan catatan favoritmu agar mudah ditemukan.</p>
        </div>

        <button type="button" class="pl-btn" id="plOpenModal">
            + Buat Playlist
        </button>
    </div>

    <?php if (empty($playlists)): ?>

        <p class="pl-empty">Belum ada playlist. Buat yang pertama!</p>

    <?php else: ?>

        <div class="pl-grid">
            <?php foreach ($playlists as $p): ?>

                <article class="pl-card">
                    <h3><?= htmlspecialchars($p['nama']) ?></h3>
                    <p><?= htmlspecialchars($p['deskripsi'] ?: 'Tanpa deskripsi') ?></p>

                    <div class="pl-footer">
                        <span><?= (int) $p['jumlah'] ?> catatan</span>

                        <form method="post"
                            action="<?= BASE_URL ?>/index.php?page=koleksi"
                            onsubmit="return confirm('Hapus playlist ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="pl-delete">Hapus</button>
                        </form>
                    </div>
                </article>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>


<div class="pl-modal" id="plModal" hidden>

    <form method="post"
        action="<?= BASE_URL ?>/index.php?page=koleksi"
        class="pl-modal-box">

        <h2>Buat Playlist Baru</h2>

        <input type="hidden" name="action" value="create">

        <label for="plNama">Nama playlist</label>
        <input type="text" id="plNama" name="nama" required maxlength="60">

        <label for="plDeskripsi">Deskripsi</label>
        <textarea id="plDeskripsi" name="deskripsi" rows="3" maxlength="150"></textarea>

        <div class="pl-modal-actions">
            <button type="button" class="pl-btn-light" id="plCloseModal">Batal</button>
            <button type="submit" class="pl-btn">Simpan</button>
        </div>

    </form>

</div>