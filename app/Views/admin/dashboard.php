<?php

/** @var int $totalCatatan */
/** @var int $totalPengguna */
/** @var int $totalUpvote */
/** @var array $pengguna */
/** @var array $laporan */

function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!-- STATISTIK -->

<section class="stat-grid">

    <div class="stat-card">
        <strong><?= $totalCatatan; ?></strong>
        <span>Total catatan</span>
    </div>

    <div class="stat-card">
        <strong><?= $totalPengguna; ?></strong>
        <span>Total pengguna</span>
    </div>

    <div class="stat-card">
        <strong><?= $totalUpvote; ?></strong>
        <span>Total upvote</span>
    </div>

</section>


<!-- KELOLA PENGGUNA-->

<section class="content-section">

    <div class="section-heading">

        <h2>Kelola pengguna</h2>

        <a href="<?= BASE_URL ?>/../kelola_pengguna/pengguna.php">
            Lihat semua
        </a>

    </div>

    <div class="table-card">

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach (array_slice($pengguna, 0, 5) as $user): ?>

                        <tr>

                            <td><?= e($user['nama']); ?></td>

                            <td><?= e($user['nim']); ?></td>

                            <td><?= e($user['jurusan']); ?></td>

                            <td>

                                <span class="status-user <?= $user['status'] === 'Aktif'
                                                                ? 'status-active'
                                                                : 'status-blocked'; ?>">

                                    <?= e($user['status']); ?>

                                </span>

                            </td>

                            <td>

                                <div class="action-wrap">

                                    <button
                                        class="action-button dropdown-trigger"
                                        type="button">

                                        Aksi <span>▼</span>

                                    </button>

                                    <div class="action-menu">

                                        <button
                                            class="menu-item view-user"
                                            type="button"
                                            data-name="<?= e($user['nama']); ?>"
                                            data-nim="<?= e($user['nim']); ?>"
                                            data-jurusan="<?= e($user['jurusan']); ?>"
                                            data-status="<?= e($user['status']); ?>">

                                            Lihat

                                        </button>

                                        <form method="POST">

                                            <input
                                                type="hidden" name="id" value="<?= $user['id']; ?>">

                                            <input
                                                type="hidden" name="user_action" value="<?= $user['status'] === 'Aktif'
                                                                                            ? 'block'
                                                                                            : 'unblock'; ?>">

                                            <button
                                                class="menu-item"
                                                type="submit">

                                                <?= $user['status'] === 'Aktif'
                                                    ? 'Blokir'
                                                    : 'Buka blokir'; ?>

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>


<!-- RINGKASAN LAPORAN-->

<section class="report-card">

    <div>

        <h2>Catatan dilaporkan</h2>

        <p>
            Terdapat <strong><?= count($laporan); ?></strong>
            catatan yang perlu diperiksa.
        </p>

    </div>

    <button class="primary-button" id="report-button">
        Periksa laporan
    </button>

</section>


<!-- DAFTAR LAPORAN -->

<section class="report-list" id="report-list">

    <div class="section-heading">

        <h2>Daftar laporan</h2>

    </div>

    <?php if (count($laporan) > 0): ?>

        <?php foreach ($laporan as $note): ?>

            <div class="reported-item">

                <div class="reported-info">

                    <h3><?= e($note['judul']); ?></h3>

                    <p>
                        Penulis: <?= e($note['penulis']); ?>
                    </p>

                    <p>
                        Alasan: <?= e($note['alasan']); ?>
                    </p>

                </div>

                <div class="report-actions">

                    <button
                        class="secondary-button view-note"
                        type="button"
                        data-title="<?= e($note['judul']); ?>"
                        data-author="<?= e($note['penulis']); ?>"
                        data-content="<?= e($note['isi']); ?>"
                        data-reason="<?= e($note['alasan']); ?>">

                        Lihat isi

                    </button>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $note['id']; ?>">

                        <input
                            type="hidden"
                            name="note_action"
                            value="publish">

                        <button class="primary-button" type="submit">
                            Terbitkan
                        </button>

                    </form>

                    <form
                        method="POST"
                        onsubmit="return confirm('Hapus catatan ini?');">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $note['id']; ?>">

                        <input
                            type="hidden"
                            name="note_action"
                            value="delete">

                        <button class="danger-button" type="submit">
                            Hapus
                        </button>

                    </form>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty-state">
            Tidak ada catatan yang dilaporkan.
        </div>

    <?php endif; ?>

</section>


<!-- DAL DETAIL PENGGUNA -->
<div class="modal-overlay" id="user-modal">

    <div class="modal">

        <button class="modal-close" data-close="user-modal">
            ×
        </button>

        <h2>Detail pengguna</h2>

        <div class="detail-list">

            <div>
                <span>Nama</span>
                <strong id="modal-user-name"></strong>
            </div>

            <div>
                <span>NIM</span>
                <strong id="modal-user-nim"></strong>
            </div>

            <div>
                <span>Jurusan</span>
                <strong id="modal-user-jurusan"></strong>
            </div>

            <div>
                <span>Status</span>
                <strong id="modal-user-status"></strong>
            </div>

        </div>

    </div>

</div>


<!-- MODAL DETAIL CATATAN -->
<div class="modal-overlay" id="note-modal">

    <div class="modal modal-large">

        <button class="modal-close" data-close="note-modal">
            ×
        </button>

        <h2 id="modal-note-title"></h2>

        <p class="modal-author" id="modal-note-author"></p>

        <div
            class="note-content"
            id="modal-note-content">
        </div>

        <div class="report-box">

            <strong>Alasan laporan</strong>

            <p id="modal-note-reason"></p>

        </div>

    </div>

</div>