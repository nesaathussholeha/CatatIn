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

        document.querySelectorAll('#user-table tbody tr')
            .forEach(row => {

                row.style.display =
                    row.textContent.toLowerCase().includes(keyword)
                        ? ''
                        : 'none';

            });

    });


    /* MODAL */

    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }


    /* TAMBAH */

    document.getElementById('open-add-modal')
        ?.addEventListener('click', function () {

            openModal('add-modal');

        });


    /* EDIT */

    document.querySelectorAll('.edit-user')
        .forEach(button => {

            button.addEventListener('click', function () {

                document.getElementById('edit-id').value =
                    this.dataset.id;

                document.getElementById('edit-name').value =
                    this.dataset.name;

                document.getElementById('edit-nim').value =
                    this.dataset.nim;

                document.getElementById('edit-major').value =
                    this.dataset.major;

                openModal('edit-modal');

            });

        });


    /* LIHAT */

    document.querySelectorAll('.view-user')
        .forEach(button => {

            button.addEventListener('click', function () {

                document.getElementById('view-name').textContent =
                    this.dataset.name;

                document.getElementById('view-nim').textContent =
                    this.dataset.nim;

                document.getElementById('view-major').textContent =
                    this.dataset.major;

                document.getElementById('view-status').textContent =
                    this.dataset.status;

                openModal('view-modal');

            });

        });


    /* TUTUP MODAL */

    document.querySelectorAll('.modal-close')
        .forEach(button => {

            button.addEventListener('click', function () {

                document.getElementById(
                    this.dataset.close
                ).classList.remove('show');

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