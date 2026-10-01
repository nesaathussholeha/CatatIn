document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       SIDEBAR
    ========================= */

    const hamburger = document.getElementById('hamburger-btn');
    const closeBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', openSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }


    /* =========================
       DROPDOWN
    ========================= */

    document.querySelectorAll('.dropdown-trigger').forEach(button => {

        button.addEventListener('click', function (event) {

            event.stopPropagation();

            const menu = this.nextElementSibling;

            document.querySelectorAll('.action-menu').forEach(item => {

                if (item !== menu) {
                    item.classList.remove('show');
                }

            });

            menu.classList.toggle('show');
        });

    });


    document.addEventListener('click', function () {

        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.classList.remove('show');
        });

    });


    /* =========================
       MODAL USER
    ========================= */

    const userModal = document.getElementById('user-modal');

    document.querySelectorAll('.view-user').forEach(button => {

        button.addEventListener('click', function () {

            document.getElementById('modal-user-name').textContent =
                this.dataset.name;

            document.getElementById('modal-user-nim').textContent =
                this.dataset.nim;

            document.getElementById('modal-user-jurusan').textContent =
                this.dataset.jurusan;

            document.getElementById('modal-user-status').textContent =
                this.dataset.status;

            userModal.classList.add('show');

        });

    });


    /* =========================
       MODAL CATATAN
    ========================= */

    const noteModal = document.getElementById('note-modal');

    document.querySelectorAll('.view-note').forEach(button => {

        button.addEventListener('click', function () {

            document.getElementById('modal-note-title').textContent =
                this.dataset.title;

            document.getElementById('modal-note-author').textContent =
                'Penulis: ' + this.dataset.author;

            document.getElementById('modal-note-content').textContent =
                this.dataset.content;

            document.getElementById('modal-note-reason').textContent =
                this.dataset.reason || 'Tidak ada alasan laporan.';

            noteModal.classList.add('show');

        });

    });


    /* =========================
       TUTUP MODAL
    ========================= */

    document.querySelectorAll('.modal-close').forEach(button => {

        button.addEventListener('click', function () {

            const id = this.dataset.close;

            document.getElementById(id).classList.remove('show');

        });

    });


    document.querySelectorAll('.modal-overlay').forEach(modal => {

        modal.addEventListener('click', function (event) {

            if (event.target === this) {
                this.classList.remove('show');
            }

        });

    });


    /* =========================
       LAPORAN
    ========================= */

    const reportButton = document.getElementById('report-button');
    const reportList = document.getElementById('report-list');

    if (reportButton) {

        reportButton.addEventListener('click', function () {

            reportList.classList.toggle('show');

            this.textContent =
                reportList.classList.contains('show')
                    ? 'Tutup laporan'
                    : 'Periksa laporan';

        });

    }

});