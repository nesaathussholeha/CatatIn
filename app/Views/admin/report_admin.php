
<?php
// Data laporan dari Controller
$laporanJson = json_encode(
    $laporan ?? [],
    JSON_UNESCAPED_UNICODE |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);
?>

<div class="report-admin-page">

    <!-- JUDUL HALAMAN -->
    <div class="report-page-header">
        <h1>Laporan Masuk</h1>
        <p>Kelola dan tinjau laporan catatan dari pengguna.</p>
    </div>

    <!-- TAB FILTER STATUS -->
    <div class="filter-tabs" id="filter-tabs" role="tablist">

        <button
            type="button"
            class="tab-btn active"
            data-filter="semua"
            role="tab"
            aria-selected="true">
            Semua
        </button>

        <button
            type="button"
            class="tab-btn"
            data-filter="menunggu"
            role="tab"
            aria-selected="false">
            Menunggu
        </button>

        <button
            type="button"
            class="tab-btn"
            data-filter="diproses"
            role="tab"
            aria-selected="false">
            Diproses
        </button>

        <button
            type="button"
            class="tab-btn"
            data-filter="selesai"
            role="tab"
            aria-selected="false">
            Selesai
        </button>

    </div>


    <!-- TABEL LAPORAN -->
    <section class="table-card">

        <div class="table-scroll">

            <table class="report-table">

                <thead>
                    <tr>
                        <th class="col-catatan">Catatan</th>
                        <th class="col-center">Pelapor</th>
                        <th class="col-center">Alasan</th>
                        <th class="col-center">Status</th>
                        <th class="col-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="report-body">
                    <!-- Data akan ditampilkan melalui JavaScript -->
                </tbody>

            </table>

        </div>

    </section>


    <!-- DROPDOWN STATUS -->
    <div
        class="status-menu"
        id="status-menu"
        role="menu"
        hidden>

        <button
            type="button"
            class="status-option opt-menunggu"
            data-status="menunggu"
            role="menuitem">
            Menunggu
        </button>

        <button
            type="button"
            class="status-option opt-diproses"
            data-status="diproses"
            role="menuitem">
            Diproses
        </button>

        <button
            type="button"
            class="status-option opt-selesai"
            data-status="selesai"
            role="menuitem">
            Selesai
        </button>

    </div>


    <!-- MODAL DETAIL -->
    <div
        class="modal-backdrop"
        id="detail-modal"
        aria-hidden="true">

        <div
            class="modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="detail-title">

            <div class="modal-header">

                <h2 id="detail-title">Detail Laporan</h2>

                <button
                    type="button"
                    class="modal-close"
                    data-close
                    aria-label="Tutup">

                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>

                    </svg>

                </button>

            </div>

            <div
                class="modal-body"
                id="detail-body">
            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-close>
                    Tutup
                </button>

            </div>

        </div>

    </div>


    <!-- MODAL KONFIRMASI HAPUS -->
    <div
        class="modal-backdrop"
        id="delete-modal"
        aria-hidden="true">

        <div
            class="modal modal-sm"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="delete-title">

            <div class="modal-header">

                <h2 id="delete-title">Hapus Laporan?</h2>

            </div>

            <div class="modal-body">

                <p
                    class="confirm-text"
                    id="delete-text">
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-close>
                    Batal
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirm-delete">
                    Ya, Hapus
                </button>

            </div>

        </div>

    </div>


    <!-- TOAST NOTIFICATION -->
    <div
        class="toast"
        id="toast"
        role="status"
        aria-live="polite">
    </div>


    <!-- DATA LAPORAN DARI CONTROLLER -->
    <script>
        window.LAPORAN_DATA = <?= $laporanJson ?: '[]' ?>;
    </script>

</div>
