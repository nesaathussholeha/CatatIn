
<div class="kelola-pengguna">
    <!-- TAB LAPORAN -->
    <div class="kp-tabs">
        <button type="button" class="kp-tab aktif" data-tab="semua">
            Semua Laporan 
        </button>

        <button type="button" class="kp-tab" data-tab="menunggu">
            Menunggu Tinjau
        </button>

        <button type="button" class="kp-tab" data-tab="selesai">
            Selesai 
        </button>
    </div>

    <!-- PENCARIAN DAN FILTER -->
    <div class="kp-filter">
        <input
            type="search"
            id="cari-laporan"
            placeholder="Cari nama pengguna atau ID laporan..."
        >

        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Selesai">Selesai</option>
        </select>
    </div>

    <!-- TABEL LAPORAN -->
    <div class="kp-table-wrapper">

        <table class="kp-table">
            <thead>
                <tr>
                    <th>Laporan</th>
                    <th>Pelapor</th>
                    <th>Pengguna Terlapor</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>

            <tbody id="daftar-laporan">

                <?php foreach ($daftarLaporan as $laporan): ?>

                    <tr
                        class="kp-row"
                        data-id="<?= htmlspecialchars($laporan['id']) ?>"
                        data-status="<?= htmlspecialchars($laporan['status']) ?>"
                        data-status-akun="<?= htmlspecialchars($laporan['status_akun']) ?>"
                        data-cari="<?= htmlspecialchars(strtolower(
                            $laporan['id'] . ' ' .
                            $laporan['pelapor'] . ' ' .
                            $laporan['terlapor'] . ' ' .
                            $laporan['username_pelapor'] . ' ' .
                            $laporan['username_terlapor']
                        )) ?>"
                        data-pelapor="<?= htmlspecialchars($laporan['pelapor']) ?>"
                        data-username-pelapor="<?= htmlspecialchars($laporan['username_pelapor']) ?>"
                        data-terlapor="<?= htmlspecialchars($laporan['terlapor']) ?>"
                        data-username-terlapor="<?= htmlspecialchars($laporan['username_terlapor']) ?>"
                        data-alasan="<?= htmlspecialchars($laporan['alasan']) ?>"
                        data-jumlah="<?= (int) $laporan['jumlah_laporan'] ?>"
                        data-tanggal="<?= htmlspecialchars($laporan['tanggal']) ?>"
                        data-waktu="<?= htmlspecialchars($laporan['waktu']) ?>"
                        data-isi="<?= htmlspecialchars($laporan['isi']) ?>"
                        data-bukti="<?= htmlspecialchars($laporan['bukti']) ?>"
                    >

                        <td>
                            <strong><?= htmlspecialchars($laporan['id']) ?></strong>
                            <small><?= htmlspecialchars($laporan['tanggal']) ?></small>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($laporan['pelapor']) ?></strong>
                            <small><?= htmlspecialchars($laporan['username_pelapor']) ?></small>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($laporan['terlapor']) ?></strong>
                            <small><?= htmlspecialchars($laporan['username_terlapor']) ?></small>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($laporan['alasan']) ?></strong>
                            <small><?= (int) $laporan['jumlah_laporan'] ?> laporan</small>
                        </td>

                        <td>
                            <span class="kp-status <?= strtolower($laporan['status']) ?>">
                                <?= htmlspecialchars($laporan['status']) ?>
                            </span>
                        </td>

                        <td class="kp-actions">

                            <?php if ($laporan['status_akun'] === 'aktif'): ?>

                                <button type="button"
                                    class="kp-btn nonaktifkan"
                                    data-action="nonaktifkan">
                                    Nonaktifkan
                                </button>

                                <button type="button"
                                    class="kp-btn blokir"
                                    data-action="blokir">
                                    Blokir
                                </button>

                            <?php elseif ($laporan['status_akun'] === 'nonaktif'): ?>

                                <button type="button"
                                    class="kp-btn aktifkan"
                                    data-action="aktifkan">
                                    Aktifkan
                                </button>

                                <button type="button"
                                    class="kp-btn blokir"
                                    data-action="blokir">
                                    Blokir
                                </button>

                            <?php elseif ($laporan['status_akun'] === 'diblokir'): ?>

                                <button type="button"
                                    class="kp-btn buka-blokir"
                                    data-action="buka-blokir">
                                    Buka Blokir
                                </button>

                            <?php endif; ?>

                            <button type="button"
                                class="kp-link"
                                data-action="detail">
                                Detail
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

        <div class="kp-empty" id="laporan-kosong" hidden>
            Tidak ada laporan yang cocok.
        </div>

        <div class="kp-table-footer">
            <span id="jumlah-laporan">
                Menampilkan <?= $totalLaporan ?> dari <?= $totalLaporan ?> laporan
            </span>

            <span class="kp-pagination">
                ‹ &nbsp; 1 &nbsp; ›
            </span>
        </div>

    </div>

    <!-- DETAIL LAPORAN -->
    <section class="kp-detail" id="detail-laporan">

        <div class="kp-detail-header">
            <h2>Detail Laporan · <span id="detail-id">-</span></h2>

            <span class="kp-status menunggu" id="detail-status">
                Menunggu Peninjauan
            </span>
        </div>

        <p class="kp-detail-meta" id="detail-meta"></p>

        <div class="kp-detail-content">

            <div class="kp-detail-message">
                <strong>Isi laporan</strong>

                <p id="detail-isi"></p>

                <button type="button" class="kp-link" id="detail-bukti">
                    Bukti terlampir
                </button>
            </div>

            <div class="kp-detail-user">
                <small>Akun terlapor</small>

                <strong id="detail-pengguna"></strong>

                <small id="detail-jumlah"></small>
            </div>

        </div>

        <div class="kp-detail-footer">

            <p>
                Nonaktifkan: akses sementara dihentikan.
                Blokir: akses akun dibatasi karena pelanggaran.
            </p>

            <div>
                <button type="button"
                    class="kp-btn tolak"
                    id="tolak-laporan">
                    Tolak Laporan
                </button>

                <button type="button"
                    class="kp-btn tinjau"
                    id="tinjau-bukti">
                    Tinjau Bukti
                </button>
            </div>

        </div>

    </section>

    <div class="kp-toast" id="kp-toast"></div>

</div>
