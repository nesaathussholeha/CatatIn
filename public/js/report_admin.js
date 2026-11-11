
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. KONFIGURASI DAN STATE
    // ==========================================

    const STORAGE_KEY = 'catatin_laporan_state_v1';

    const STATUS_LABEL = {
        menunggu: 'Menunggu',
        diproses: 'Diproses',
        selesai: 'Selesai'
    };

    const state = {
        loading: true,
        filter: 'semua',
        reports: [],
        menuForId: null,
        detailId: null,
        deleteId: null
    };

    // ==========================================
    // 2. MENGAMBIL DAN MENYIMPAN DATA
    // ==========================================

    function loadReports() {
        const fromServer = Array.isArray(window.LAPORAN_DATA)
            ? window.LAPORAN_DATA
            : [];

        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY));

            if (saved && Array.isArray(saved.reports)) {
                return saved.reports;
            }
        } catch (error) {
            console.warn('Data laporan lokal tidak dapat dibaca:', error);
        }

        return fromServer.slice();
    }

    function persist() {
        try {
            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({ reports: state.reports })
            );
        } catch (error) {
            console.warn('Data laporan gagal disimpan:', error);
        }
    }

    // ==========================================
    // 3. MENGAMBIL ELEMEN HTML
    // ==========================================

    const tbody = document.getElementById('report-body');

    const tabsWrap = document.getElementById('filter-tabs');
    const tabButtons = tabsWrap
        ? tabsWrap.querySelectorAll('.tab-btn')
        : [];

    const statusMenu = document.getElementById('status-menu');

    const detailModal = document.getElementById('detail-modal');
    const detailBody = document.getElementById('detail-body');

    const deleteModal = document.getElementById('delete-modal');
    const deleteText = document.getElementById('delete-text');
    const confirmDeleteBtn = document.getElementById('confirm-delete');

    const toastEl = document.getElementById('toast');

    // ==========================================
    // 4. FUNGSI BANTUAN
    // ==========================================

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatTanggal(iso) {
        if (!iso) return '-';

        const date = new Date(iso + 'T00:00:00');

        if (isNaN(date.getTime())) {
            return iso;
        }

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function findReport(id) {
        return state.reports.find(function (report) {
            return Number(report.id) === Number(id);
        });
    }

    function getStatus(status) {
        return STATUS_LABEL[status] ? status : 'menunggu';
    }

    // ==========================================
    // 5. TOAST NOTIFICATION
    // ==========================================

    let toastTimer = null;

    function showToast(message) {
        if (!toastEl) return;

        toastEl.textContent = message;
        toastEl.classList.add('show');

        clearTimeout(toastTimer);

        toastTimer = setTimeout(function () {
            toastEl.classList.remove('show');
        }, 2400);
    }

    // ==========================================
    // 6. ICON
    // ==========================================

    const ICON_TRASH =
        '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" ' +
        'stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
        'stroke-linejoin="round">' +
        '<polyline points="3 6 5 6 21 6"></polyline>' +
        '<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>' +
        '<path d="M10 11v6"></path>' +
        '<path d="M14 11v6"></path>' +
        '<path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>' +
        '</svg>';

    const ICON_EMPTY =
        '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" ' +
        'stroke="currentColor" stroke-width="1.5" stroke-linecap="round" ' +
        'stroke-linejoin="round">' +
        '<path d="M22 12h-6l-2 3h-4l-2-3H2"></path>' +
        '<path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>' +
        '</svg>';

    // ==========================================
    // 7. FILTER LAPORAN
    // ==========================================

    function getFiltered() {
        if (state.filter === 'semua') {
            return state.reports;
        }

        return state.reports.filter(function (report) {
            return getStatus(report.status) === state.filter;
        });
    }

    // ==========================================
    // 8. TAMPILAN LOADING
    // ==========================================

    function renderLoading() {
        if (!tbody) return;

        let html = '';

        for (let i = 0; i < 3; i++) {
            html +=
                '<tr class="skeleton-row">' +
                    '<td><span class="skeleton w-60"></span></td>' +
                    '<td><span class="skeleton w-50"></span></td>' +
                    '<td><span class="skeleton w-50"></span></td>' +
                    '<td><span class="skeleton w-pill"></span></td>' +
                    '<td><span class="skeleton w-50"></span></td>' +
                '</tr>';
        }

        tbody.innerHTML = html;
    }

    // ==========================================
    // 9. TAMPILAN KETIKA DATA KOSONG
    // ==========================================

    function renderEmpty() {
        if (!tbody) return;

        const isAll = state.filter === 'semua';

        const title = isAll
            ? 'Belum ada laporan masuk'
            : 'Tidak ada laporan "' +
                (STATUS_LABEL[state.filter] || state.filter) + '"';

        const description = isAll
            ? 'Laporan dari pengguna akan tampil di sini.'
            : 'Coba pilih tab status yang lain.';

        tbody.innerHTML =
            '<tr class="state-row">' +
                '<td colspan="5">' +
                    '<div class="empty-state">' +
                        ICON_EMPTY +
                        '<strong>' + escapeHtml(title) + '</strong>' +
                        '<span>' + escapeHtml(description) + '</span>' +
                    '</div>' +
                '</td>' +
            '</tr>';
    }

    // ==========================================
    // 10. MENAMPILKAN DATA LAPORAN
    // ==========================================

    function renderRows(animate) {
        if (!tbody) return;

        const rows = getFiltered();

        if (rows.length === 0) {
            renderEmpty();
            return;
        }

        tbody.innerHTML = rows.map(function (report) {

            const status = getStatus(report.status);

            return (
                '<tr data-id="' + Number(report.id) + '"' +
                    (animate ? ' class="row-enter"' : '') + '>' +

                    '<td class="cell-catatan">' +
                        escapeHtml(report.catatan) +
                    '</td>' +

                    '<td class="col-center cell-muted">' +
                        escapeHtml(report.pelapor) +
                    '</td>' +

                    '<td class="col-center cell-muted">' +
                        escapeHtml(report.alasan) +
                    '</td>' +

                    '<td class="col-center">' +
                        '<button type="button" ' +
                            'class="status-pill status-' + status + '" ' +
                            'data-action="status" ' +
                            'aria-haspopup="menu">' +
                            STATUS_LABEL[status] +
                        '</button>' +
                    '</td>' +

                    '<td class="col-center">' +
                        '<div class="action-cell">' +

                            '<button type="button" ' +
                                'class="link-detail" ' +
                                'data-action="detail">' +
                                'Detail' +
                            '</button>' +

                            '<button type="button" ' +
                                'class="link-delete" ' +
                                'data-action="delete">' +
                                ICON_TRASH + 'Hapus' +
                            '</button>' +

                        '</div>' +
                    '</td>' +

                '</tr>'
            );

        }).join('');
    }

    // ==========================================
    // 11. MENGATUR TAB FILTER
    // ==========================================

    function renderTabs() {
        if (!tabButtons.length) return;

        tabButtons.forEach(function (button) {

            const active = button.dataset.filter === state.filter;

            button.classList.toggle('active', active);

            button.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });
    }

    function render(animate) {
        renderTabs();

        if (state.loading) {
            renderLoading();
        } else {
            renderRows(animate);
        }
    }

    if (tabsWrap) {
        tabsWrap.addEventListener('click', function (event) {

            const button = event.target.closest('.tab-btn');

            if (!button || state.loading) return;

            state.filter = button.dataset.filter;

            closeStatusMenu();

            render(true);
        });
    }

    // ==========================================
    // 12. DROPDOWN STATUS
    // ==========================================

    function openStatusMenu(id, anchor) {
        if (!statusMenu) return;

        const report = findReport(id);

        if (!report) return;

        state.menuForId = id;

        statusMenu.querySelectorAll('.status-option').forEach(
            function (option) {

                option.classList.toggle(
                    'selected',
                    option.dataset.status === getStatus(report.status)
                );

            }
        );

        statusMenu.hidden = false;

        const rect = anchor.getBoundingClientRect();

        const menuW = statusMenu.offsetWidth;
        const menuH = statusMenu.offsetHeight;

        let left = rect.left + rect.width / 2 - menuW / 2;

        left = Math.max(
            8,
            Math.min(left, window.innerWidth - menuW - 8)
        );

        let top = rect.bottom + 6;

        if (top + menuH > window.innerHeight - 8) {
            top = rect.top - menuH - 6;
        }

        statusMenu.style.left = left + 'px';
        statusMenu.style.top = top + 'px';
    }

    function closeStatusMenu() {
        if (!statusMenu) return;

        state.menuForId = null;
        statusMenu.hidden = true;
    }

    if (statusMenu) {
        statusMenu.addEventListener('click', function (event) {

            const option = event.target.closest('.status-option');

            if (!option || state.menuForId === null) return;

            const report = findReport(state.menuForId);
            const newStatus = option.dataset.status;

            closeStatusMenu();

            if (!report || !STATUS_LABEL[newStatus]) return;

            if (report.status === newStatus) return;

            report.status = newStatus;

            persist();
            render(false);

            showToast(
                'Status "' + report.catatan +
                '" diubah menjadi ' + STATUS_LABEL[newStatus]
            );
        });
    }

    // Menutup dropdown ketika klik di luar dropdown

    document.addEventListener('click', function (event) {

        if (state.menuForId === null) return;

        if (
            event.target.closest('#status-menu') ||
            event.target.closest('[data-action="status"]')
        ) {
            return;
        }

        closeStatusMenu();
    });

    window.addEventListener('resize', closeStatusMenu);
    window.addEventListener('scroll', closeStatusMenu, true);

    // ==========================================
    // 13. AKSI PADA TABEL
    // ==========================================

    if (tbody) {
        tbody.addEventListener('click', function (event) {

            const actionEl = event.target.closest('[data-action]');

            if (!actionEl) return;

            const tr = actionEl.closest('tr');

            if (!tr) return;

            const id = Number(tr.dataset.id);

            if (!Number.isFinite(id)) return;

            const action = actionEl.dataset.action;

            if (action === 'status') {

                event.stopPropagation();

                if (state.menuForId === id) {
                    closeStatusMenu();
                } else {
                    openStatusMenu(id, actionEl);
                }

            } else if (action === 'detail') {

                openDetail(id);

            } else if (action === 'delete') {

                openDelete(id);

            }

        });
    }

    // ==========================================
    // 14. MODAL DETAIL DAN HAPUS
    // ==========================================

    function openModal(modal) {
        if (!modal) return;

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    function closeAllModals() {
        closeModal(detailModal);
        closeModal(deleteModal);

        state.detailId = null;
        state.deleteId = null;
    }

    [detailModal, deleteModal].forEach(function (modal) {

        if (!modal) return;

        modal.addEventListener('click', function (event) {

            if (
                event.target === modal ||
                event.target.closest('[data-close]')
            ) {
                closeAllModals();
            }

        });

    });

    // Menutup modal dengan tombol Escape

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeAllModals();
            closeStatusMenu();
        }

    });

    // ==========================================
    // 15. MODAL DETAIL LAPORAN
    // ==========================================

    function openDetail(id) {
        const report = findReport(id);

        if (!report || !detailBody) return;

        state.detailId = id;

        const status = getStatus(report.status);

        detailBody.innerHTML =
            '<dl class="detail-list">' +

                '<dt>Catatan</dt>' +
                '<dd>' + escapeHtml(report.catatan) + '</dd>' +

                '<dt>Pelapor</dt>' +
                '<dd>' + escapeHtml(report.pelapor) + '</dd>' +

                '<dt>Alasan</dt>' +
                '<dd>' + escapeHtml(report.alasan) + '</dd>' +

                '<dt>Tanggal</dt>' +
                '<dd>' + escapeHtml(formatTanggal(report.tanggal)) + '</dd>' +

                '<dt>Status</dt>' +
                '<dd>' +
                    '<span class="status-pill status-' + status + '" ' +
                        'style="pointer-events:none">' +
                        escapeHtml(STATUS_LABEL[status]) +
                    '</span>' +
                '</dd>' +

                '<dt>Keterangan</dt>' +
                '<dd class="desc">' +
                    escapeHtml(report.deskripsi || '-') +
                '</dd>' +

            '</dl>';

        openModal(detailModal);
    }

    // ==========================================
    // 16. MODAL KONFIRMASI HAPUS
    // ==========================================

    function openDelete(id) {
        const report = findReport(id);

        if (!report || !deleteText) return;

        state.deleteId = id;

        deleteText.textContent =
            'Laporan untuk "' + report.catatan +
            '" dari ' + report.pelapor +
            ' akan dihapus dan tidak dapat dikembalikan.';

        openModal(deleteModal);
    }

    // ==========================================
    // 17. PROSES HAPUS LAPORAN
    // ==========================================

    if (confirmDeleteBtn) {

        confirmDeleteBtn.addEventListener('click', function () {

            if (state.deleteId === null) return;

            const id = state.deleteId;
            const report = findReport(id);

            closeModal(deleteModal);

            state.deleteId = null;

            const row = tbody
                ? tbody.querySelector('tr[data-id="' + id + '"]')
                : null;

            function finalizeDelete() {

                state.reports = state.reports.filter(function (item) {
                    return Number(item.id) !== Number(id);
                });

                persist();

                render(false);

                if (report) {
                    showToast(
                        'Laporan "' + report.catatan +
                        '" berhasil dihapus'
                    );
                }
            }

            if (row) {

                row.classList.add('row-leave');

                setTimeout(finalizeDelete, 250);

            } else {

                finalizeDelete();

            }

        });

    }

    // ==========================================
    // 18. INISIALISASI
    // ==========================================

    state.reports = loadReports();

    render(false);

    setTimeout(function () {

        state.loading = false;

        render(true);

    }, 600);

});
