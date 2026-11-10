
<?php

function e($text)
{
    return htmlspecialchars((string) ($text ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<section class="page-card">

    <div class="page-card-header">

        <h2>Daftar catatan</h2>

        <div class="toolbar">

            <input
                type="text"
                id="search-input"
                placeholder="Cari judul atau penulis...">

            <button
                type="button"
                class="add-button"
                id="open-add-modal">

                + Tambah catatan

            </button>

        </div>

    </div>

    <div class="table-container">

        <table id="catatan-table">

            <thead>
                <tr>
                    <th>Judul catatan</th>
                    <th>Penulis</th>
                    <th>Jurusan</th>
                    <th>Upvote</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($catatan as $note): ?>

                    <tr>

                        <td class="title-cell">
                            <?= e($note['judul']); ?>
                        </td>

                        <td>
                            <?= e($note['penulis']); ?>
                        </td>

                        <td>
                            <?= e($note['jurusan']); ?>
                        </td>

                        <td>
                            <?= (int) $note['upvote']; ?>
                        </td>

                        <td>

                            <span class="status-badge status-<?= strtolower(e($note['status'])); ?>">
                                <?= e($note['status']); ?>
                            </span>

                        </td>

                        <td>

                            <div class="action-wrap">

                                <button
                                    type="button"
                                    class="action-button dropdown-trigger">

                                    Aksi <span>▼</span>

                                </button>

                                <div class="action-menu">

                                    <!-- LIHAT -->
                                    <button
                                        type="button"
                                        class="menu-item view-note"
                                        data-title="<?= e($note['judul']); ?>"
                                        data-author="<?= e($note['penulis']); ?>"
                                        data-major="<?= e($note['jurusan']); ?>"
                                        data-content="<?= e($note['isi']); ?>"
                                        data-reason="<?= e($note['alasan']); ?>">

                                        Lihat

                                    </button>

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        class="menu-item edit-note"
                                        data-id="<?= (int) $note['id']; ?>"
                                        data-title="<?= e($note['judul']); ?>"
                                        data-author="<?= e($note['penulis']); ?>"
                                        data-major="<?= e($note['jurusan']); ?>"
                                        data-content="<?= e($note['isi']); ?>">

                                        Edit

                                    </button>

                                    <!-- UBAH STATUS -->
                                    <form method="POST" action="<?= BASE_URL ?>/?page=kelola_catatan">

                                        <input type="hidden" name="action" value="status">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $note['id']; ?>">

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="<?= $note['status'] === 'Terbit' ? 'Draft' : 'Terbit'; ?>">

                                        <button type="submit" class="menu-item">

                                            <?= $note['status'] === 'Terbit'
                                                ? 'Jadikan Draft'
                                                : 'Terbitkan'; ?>

                                        </button>

                                    </form>

                                    <!-- HAPUS -->
                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>/?page=kelola_catatan"
                                        onsubmit="return confirm('Hapus catatan ini?');">

                                        <input type="hidden" name="action" value="delete">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $note['id']; ?>">

                                        <button type="submit" class="menu-item delete-item">
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        <p id="empty-search" hidden>
            Catatan tidak ditemukan.
        </p>

    </div>

</section>


<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="add-modal">

    <div class="modal">

        <button type="button" class="modal-close" data-close="add-modal">
            ×
        </button>

        <h2>Tambah catatan</h2>

        <form method="POST" action="<?= BASE_URL ?>/?page=kelola_catatan">

            <input type="hidden" name="action" value="add">

            <label>Judul catatan</label>
            <input type="text" name="judul" required>

            <label>Penulis</label>
            <input type="text" name="penulis" required>

            <label>Jurusan</label>
            <input type="text" name="jurusan" required>

            <label>Isi catatan</label>
            <textarea name="isi" rows="6" required></textarea>

            <button class="save-button" type="submit">
                Tambahkan
            </button>

        </form>

    </div>

</div>


<!-- MODAL EDIT -->
<div class="modal-overlay" id="edit-modal">

    <div class="modal">

        <button type="button" class="modal-close" data-close="edit-modal">
            ×
        </button>

        <h2>Edit catatan</h2>

        <form method="POST" action="<?= BASE_URL ?>/?page=kelola_catatan">

            <input type="hidden" name="action" value="edit">

            <input type="hidden" name="id" id="edit-id">

            <label>Judul catatan</label>
            <input type="text" name="judul" id="edit-title" required>

            <label>Penulis</label>
            <input type="text" name="penulis" id="edit-author" required>

            <label>Jurusan</label>
            <input type="text" name="jurusan" id="edit-major" required>

            <label>Isi catatan</label>
            <textarea name="isi" id="edit-content" rows="6" required></textarea>

            <button class="save-button" type="submit">
                Simpan perubahan
            </button>

        </form>

    </div>

</div>


<!-- MODAL LIHAT -->
<div class="modal-overlay" id="view-modal">

    <div class="modal modal-large">

        <button type="button" class="modal-close" data-close="view-modal">
            ×
        </button>

        <h2 id="view-title"></h2>

        <p id="view-author"></p>

        <div class="view-content" id="view-content"></div>

        <div class="report-info" id="view-reason"></div>

    </div>

</div>
