document.addEventListener('DOMContentLoaded', function () {

    /* SIDEBAR */

    const hamburger = document.getElementById('hamburger-btn');
    const closeBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    hamburger?.addEventListener('click', function () {
        sidebar.classList.add('active');
        overlay.classList.add('active');
    });

    closeBtn?.addEventListener('click', function () {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    overlay?.addEventListener('click', function () {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });


    /* DROPDOWN */

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


    /* SEARCH */

    const search = document.getElementById('search-input');

    search?.addEventListener('input', function () {

        const keyword = this.value.toLowerCase();

        document.querySelectorAll('#catatan-table tbody tr')
            .forEach(row => {

                const text = row.textContent.toLowerCase();

                row.style.display =
                    text.includes(keyword) ? '' : 'none';

            });

    });


    /* MODAL */

    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }


    /* TAMBAH */

    document.getElementById('open-add-modal')
        ?.addEventListener('click', function () {

            openModal('add-modal');

        });


    /* EDIT */

    document.querySelectorAll('.edit-note')
        .forEach(button => {

            button.addEventListener('click', function () {

                document.getElementById('edit-id').value =
                    this.dataset.id;

                document.getElementById('edit-title').value =
                    this.dataset.title;

                document.getElementById('edit-author').value =
                    this.dataset.author;

                document.getElementById('edit-major').value =
                    this.dataset.major;

                document.getElementById('edit-content').value =
                    this.dataset.content;

                openModal('edit-modal');

            });

        });


    /* LIHAT */

    document.querySelectorAll('.view-note')
        .forEach(button => {

            button.addEventListener('click', function () {

                document.getElementById('view-title').textContent =
                    this.dataset.title;

                document.getElementById('view-author').textContent =
                    'Penulis: ' + this.dataset.author +
                    ' | Jurusan: ' + this.dataset.major;

                document.getElementById('view-content').textContent =
                    this.dataset.content;

                const reason = this.dataset.reason;

                document.getElementById('view-reason').textContent =
                    reason
                        ? 'Alasan laporan: ' + reason
                        : 'Catatan ini tidak sedang dilaporkan.';

                openModal('view-modal');

            });

        });


    /* TUTUP MODAL */

    document.querySelectorAll('.modal-close')
        .forEach(button => {

            button.addEventListener('click', function () {

                closeModal(this.dataset.close);

            });

        });


    document.querySelectorAll('.modal-overlay')
        .forEach(modal => {

            modal.addEventListener('click', function (event) {

                if (event.target === this) {
                    this.classList.remove('show');
                }

            });

        });

});