document.addEventListener('DOMContentLoaded', function () {

    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
    if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

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

    function loadReports() {
        const fromServer = Array.isArray(window.LAPORAN_DATA) ? window.LAPORAN_DATA : [];
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
            if (saved && Array.isArray(saved.reports)) {
                return saved.reports;
            }
        } catch (e) {}
        return fromServer.slice();
    }

    function persist() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({ reports: state.reports }));
        } catch (e) {}
    }

    const tbody = document.getElementById('report-body');
    const tabsWrap = document.getElementById('filter-tabs');
    const tabButtons = tabsWrap.querySelectorAll('.tab-btn');

    const statusMenu = document.getElementById('status-menu');
    const detailModal = document.getElementById('detail-modal');
    const detailBody = document.getElementById('detail-body');
    const deleteModal = document.getElementById('delete-modal');
    const deleteText = document.getElementById('delete-text');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const toastEl = document.getElementById('toast');

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatTanggal(iso) {
        if (!iso) return '-';
        const d = new Date(iso + 'T00:00:00');
        if (isNaN(d)) return iso;
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function findReport(id) {
        return state.reports.find(r => Number(r.id) === Number(id));
    }

    let toastTimer = null;
    function showToast(message) {
        toastEl.textContent = message;
        toastEl.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toastEl.classList.remove('show'), 2400);
    }

    const ICON_TRASH =
        '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<polyline points="3 6 5 6 21 6"></polyline>' +
        '<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>' +
        '<path d="M10 11v6"></path><path d="M14 11v6"></path>' +
        '<path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>';

    const ICON_EMPTY =
        '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="M22 12h-6l-2 3h-4l-2-3H2"></path>' +
        '<path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>';

    function getFiltered() {
        if (state.filter === 'semua') return state.reports;
        return state.reports.filter(r => r.status === state.filter);
    }

    function renderLoading() {
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

    function renderEmpty() {
        const isAll = state.filter === 'semua';
        const title = isAll ? 'Belum ada laporan masuk' : 'Tidak ada laporan "' + STATUS_LABEL[state.filter] + '"';
        const desc = isAll
            ? 'Laporan dari pengguna akan tampil di sini.'
            : 'Coba pilih tab status yang lain.';
        tbody.innerHTML =
            '<tr class="state-row"><td colspan="5">' +
            '<div class="empty-state">' + ICON_EMPTY +
            '<strong>' + escapeHtml(title) + '</strong>' +
            '<span>' + escapeHtml(desc) + '</span></div></td></tr>';
    }

    function renderRows(animate) {
        const rows = getFiltered();
        if (rows.length === 0) {
            renderEmpty();
            return;
        }

        tbody.innerHTML = rows.map(function (r) {
            const status = STATUS_LABEL[r.status] ? r.status : 'menunggu';
            return (
                '<tr data-id="' + Number(r.id) + '"' + (animate ? ' class="row-enter"' : '') + '>' +
                    '<td class="cell-catatan">' + escapeHtml(r.catatan) + '</td>' +
                    '<td class="col-center cell-muted">' + escapeHtml(r.pelapor) + '</td>' +
                    '<td class="col-center cell-muted">' + escapeHtml(r.alasan) + '</td>' +
                    '<td class="col-center">' +
                        '<button type="button" class="status-pill status-' + status + '" data-action="status" aria-haspopup="menu">' +
                            STATUS_LABEL[status] +
                        '</button>' +
                    '</td>' +
                    '<td class="col-center">' +
                        '<div class="action-cell">' +
                            '<button type="button" class="link-detail" data-action="detail">Detail</button>' +
                            '<button type="button" class="link-delete" data-action="delete">' + ICON_TRASH + 'Hapus</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );
        }).join('');
    }

    function renderTabs() {
        tabButtons.forEach(function (btn) {
            const active = btn.dataset.filter === state.filter;
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
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

    tabsWrap.addEventListener('click', function (e) {
        const btn = e.target.closest('.tab-btn');
        if (!btn || state.loading) return;
        state.filter = btn.dataset.filter;
        closeStatusMenu();
        render(true);
    });

    function openStatusMenu(id, anchor) {
        const report = findReport(id);
        if (!report) return;

        state.menuForId = id;

        statusMenu.querySelectorAll('.status-option').forEach(function (opt) {
            opt.classList.toggle('selected', opt.dataset.status === report.status);
        });

        statusMenu.hidden = false;

        const rect = anchor.getBoundingClientRect();
        const menuW = statusMenu.offsetWidth;
        const menuH = statusMenu.offsetHeight;

        let left = rect.left + rect.width / 2 - menuW / 2;
        left = Math.max(8, Math.min(left, window.innerWidth - menuW - 8));

        let top = rect.bottom + 6;
        if (top + menuH > window.innerHeight - 8) {
            top = rect.top - menuH - 6; 
        }

        statusMenu.style.left = left + 'px';
        statusMenu.style.top = top + 'px';
    }

    function closeStatusMenu() {
        state.menuForId = null;
        statusMenu.hidden = true;
    }

    statusMenu.addEventListener('click', function (e) {
        const opt = e.target.closest('.status-option');
        if (!opt || state.menuForId === null) return;

        const report = findReport(state.menuForId);
        const newStatus = opt.dataset.status;
        closeStatusMenu();

        if (!report || report.status === newStatus) return;

        report.status = newStatus;
        persist();
        render(false);
        showToast('Status "' + report.catatan + '" diubah menjadi ' + STATUS_LABEL[newStatus]);
    });

    document.addEventListener('click', function (e) {
        if (state.menuForId === null) return;
        if (e.target.closest('#status-menu') || e.target.closest('[data-action="status"]')) return;
        closeStatusMenu();
    });

    window.addEventListener('resize', closeStatusMenu);
    window.addEventListener('scroll', closeStatusMenu, true);

    tbody.addEventListener('click', function (e) {
        const actionEl = e.target.closest('[data-action]');
        if (!actionEl) return;

        const tr = actionEl.closest('tr');
        const id = tr ? Number(tr.dataset.id) : null;
        if (id === null) return;

        const action = actionEl.dataset.action;

        if (action === 'status') {
            e.stopPropagation();
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

    function openModal(modal) {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal(modal) {
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
        modal.addEventListener('click', function (e) {
            if (e.target === modal || e.target.closest('[data-close]')) {
                closeAllModals();
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllModals();
            closeStatusMenu();
            closeSidebar();
        }
    });

    // Detail
    function openDetail(id) {
        const r = findReport(id);
        if (!r) return;
        state.detailId = id;

        detailBody.innerHTML =
            '<dl class="detail-list">' +
                '<dt>Catatan</dt><dd>' + escapeHtml(r.catatan) + '</dd>' +
                '<dt>Pelapor</dt><dd>' + escapeHtml(r.pelapor) + '</dd>' +
                '<dt>Alasan</dt><dd>' + escapeHtml(r.alasan) + '</dd>' +
                '<dt>Tanggal</dt><dd>' + escapeHtml(formatTanggal(r.tanggal)) + '</dd>' +
                '<dt>Status</dt><dd><span class="status-pill status-' + escapeHtml(r.status) + '" style="pointer-events:none">' +
                    escapeHtml(STATUS_LABEL[r.status] || r.status) + '</span></dd>' +
                '<dt>Keterangan</dt><dd class="desc">' + escapeHtml(r.deskripsi || '-') + '</dd>' +
            '</dl>';

        openModal(detailModal);
    }

    // Hapus
    function openDelete(id) {
        const r = findReport(id);
        if (!r) return;
        state.deleteId = id;
        deleteText.textContent =
            'Laporan untuk "' + r.catatan + '" dari ' + r.pelapor + ' akan dihapus dan tidak dapat dikembalikan.';
        openModal(deleteModal);
    }

    confirmDeleteBtn.addEventListener('click', function () {
        if (state.deleteId === null) return;

        const id = state.deleteId;
        const report = findReport(id);
        closeModal(deleteModal);
        state.deleteId = null;

        const row = tbody.querySelector('tr[data-id="' + id + '"]');

        function finalize() {
            state.reports = state.reports.filter(r => Number(r.id) !== Number(id));
            persist();
            render(false);
            if (report) showToast('Laporan "' + report.catatan + '" berhasil dihapus');
        }

        if (row) {
            row.classList.add('row-leave');
            setTimeout(finalize, 250);
        } else {
            finalize();
        }
    });

    state.reports = loadReports();
    render(false);

    setTimeout(function () {
        state.loading = false;
        render(true);
    }, 600);
});