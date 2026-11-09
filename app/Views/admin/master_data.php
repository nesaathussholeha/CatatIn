
<?php
$master_data = $master_data ?? [
    'jurusan' => [],
    'matakuliah' => [],
    'kategori' => []
];

$active_tab = $active_tab ?? 'jurusan';
?>

<div class="content-body">

    <!-- TAB -->
    <div class="tab-switcher">

        <button
            type="button"
            class="tab-btn <?= $active_tab === 'jurusan' ? 'active' : '' ?>"
            data-tab="jurusan">
            Jurusan
        </button>

        <button
            type="button"
            class="tab-btn <?= $active_tab === 'matakuliah' ? 'active' : '' ?>"
            data-tab="matakuliah">
            Mata Kuliah
        </button>

        <button
            type="button"
            class="tab-btn <?= $active_tab === 'kategori' ? 'active' : '' ?>"
            data-tab="kategori">
            Kategori Dokumen
        </button>

    </div>


    <!-- ACTION BAR -->
    <div class="action-bar">

        <div class="search-box">

            <iconify-icon icon="lucide:search"></iconify-icon>

            <input
                type="text"
                id="search-input"
                placeholder="Cari Data...">

        </div>

        <button
            type="button"
            class="btn-primary"
            id="btn-tambah">
            + Tambah
        </button>

    </div>


    <!-- TABEL -->
    <div class="table-card">

        <div class="table-responsive">

            <table>

                <thead>
                    <tr id="table-headers"></tr>
                </thead>

                <tbody id="table-body"></tbody>

            </table>

        </div>

    </div>

</div>


<!-- MODAL FORM -->
<div class="modal-overlay" id="modal-form">

    <div class="modal">

        <button
            type="button"
            class="modal-close"
            id="btn-close-modal">
            &times;
        </button>

        <h2 id="modal-title">Tambah Data</h2>


        <form
            action="<?= BASE_URL ?>/index.php?page=master-data"
            method="POST"
            id="form-master">

            <input type="hidden" name="action" value="save">

            <input
                type="hidden"
                name="tab"
                id="modal-tab"
                value="<?= htmlspecialchars($active_tab) ?>">

            <input
                type="hidden"
                name="id"
                id="modal-id"
                value="">


            <div class="form-body">

                <div class="form-group">

                    <label for="input-kode" id="label-kode">
                        Kode
                    </label>

                    <input
                        type="text"
                        id="input-kode"
                        name="kode"
                        required
                        placeholder="Masukkan kode">

                </div>


                <div class="form-group">

                    <label for="input-nama" id="label-nama">
                        Nama Jurusan
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="input-nama"
                        name="nama"
                        required
                        placeholder="Masukkan nama">

                </div>


                <div
                    class="form-group hidden"
                    id="group-extra">

                    <label for="input-extra">
                        Jurusan
                    </label>

                    <select id="input-extra" name="extra">

                        <?php foreach ($master_data['jurusan'] as $j): ?>

                            <option value="<?= htmlspecialchars($j['nama']) ?>">
                                <?= htmlspecialchars($j['nama']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="secondary-button"
                    id="btn-batal">
                    Batal
                </button>

                <button
                    type="submit"
                    class="primary-button">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>


<!-- FORM AKSI HAPUS / STATUS -->
<form
    id="action-form"
    action="<?= BASE_URL ?>/index.php?page=master-data"
    method="POST"
    style="display:none;">

    <input
        type="hidden"
        name="action"
        id="action-type">

    <input
        type="hidden"
        name="tab"
        id="action-tab"
        value="<?= htmlspecialchars($active_tab) ?>">

    <input
        type="hidden"
        name="id"
        id="action-id">

</form>


<!-- DATA DARI PHP KE JAVASCRIPT -->
<script>
    const initialMasterData = <?= json_encode(
        $master_data,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const initialActiveTab = <?= json_encode($active_tab) ?>;
</script>
