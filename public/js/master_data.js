
document.addEventListener("DOMContentLoaded", () => {

    let currentTab = initialActiveTab || "jurusan";
    let dataStore = initialMasterData || {};

    // DOM Elements
    const tabButtons = document.querySelectorAll(".tab-btn");
    const searchInput = document.getElementById("search-input");
    const btnTambah = document.getElementById("btn-tambah");

    const modalOverlay = document.getElementById("modal-form");
    const btnCloseModal = document.getElementById("btn-close-modal");
    const btnBatal = document.getElementById("btn-batal");

    // Sidebar Admin
    const hamburgerBtn = document.getElementById("hamburger-btn");
    const sidebarCloseBtn = document.getElementById("sidebar-close");
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.getElementById("sidebar-overlay");

    if (hamburgerBtn && sidebar && sidebarOverlay) {
        hamburgerBtn.addEventListener("click", () => {
            sidebar.classList.add("active");
            sidebarOverlay.classList.add("active");
        });
    }

    const closeSidebar = () => {
        if (sidebar) sidebar.classList.remove("active");
        if (sidebarOverlay) sidebarOverlay.classList.remove("active");
    };

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener("click", closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebar);
    }

    // Modal Elements
    const modalTitle = document.getElementById("modal-title");
    const modalTabInput = document.getElementById("modal-tab");
    const modalIdInput = document.getElementById("modal-id");

    const labelKode = document.getElementById("label-kode");
    const labelNama = document.getElementById("label-nama");

    const inputKode = document.getElementById("input-kode");
    const inputNama = document.getElementById("input-nama");

    const groupExtra = document.getElementById("group-extra");
    const inputExtra = document.getElementById("input-extra");

    const tableHeadersMap = {
        jurusan: ["Kode", "Nama Jurusan", "Status", "Pengguna", "Aksi"],
        matakuliah: [
            "Kode",
            "Nama Mata Kuliah",
            "Jurusan",
            "Status",
            "Pengguna",
            "Aksi"
        ],
        kategori: ["Kode", "Nama Kategori", "Status", "Pengguna", "Aksi"]
    };

    const formLabelsMap = {
        jurusan: {
            title: "Jurusan",
            kode: "Kode",
            nama: "Nama Jurusan"
        },
        matakuliah: {
            title: "Mata Kuliah",
            kode: "Kode Mata Kuliah",
            nama: "Nama Mata Kuliah"
        },
        kategori: {
            title: "Kategori Dokumen",
            kode: "Kode Kategori",
            nama: "Nama Kategori"
        }
    };

    // Escape teks sebelum dimasukkan ke HTML
    function escapeHTML(value) {
        return String(value ?? "").replace(/[&<>"']/g, char => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        })[char]);
    }

    // Render tabel
    function renderTable() {
        const tableHeaders = document.getElementById("table-headers");
        const tableBody = document.getElementById("table-body");

        if (!tableHeaders || !tableBody || !searchInput) return;

        const query = searchInput.value.toLowerCase();

        tableHeaders.innerHTML = (tableHeadersMap[currentTab] || [])
            .map(header => `<th>${escapeHTML(header)}</th>`)
            .join("");

        const items = dataStore[currentTab] || [];

        const filtered = items.filter(item => {
            return (
                String(item.kode || "").toLowerCase().includes(query) ||
                String(item.nama || "").toLowerCase().includes(query) ||
                String(item.jurusan || "").toLowerCase().includes(query)
            );
        });

        if (filtered.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${tableHeadersMap[currentTab].length}"
                        style="text-align:center; padding:32px; color:#71809d;">
                        Tidak ada data ditemukan.
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = filtered.map(item => {

            const statusClass = item.status
                ? "status-active"
                : "status-blocked";

            const statusText = item.status
                ? "Aktif"
                : "Non-Aktif";

            const penggunaCount = parseInt(item.pengguna) || 0;

            const id = escapeHTML(item.id);
            const kode = escapeHTML(item.kode);
            const nama = escapeHTML(item.nama);
            const jurusan = escapeHTML(item.jurusan || "-");
            const pengguna = escapeHTML(item.pengguna || "0 Catatan");

            let extraColumn = "";

            if (currentTab === "matakuliah") {
                extraColumn = `<td>${jurusan}</td>`;
            }

            let actionButtons = "";

            actionButtons += `
                <button class="btn-action edit"
                    data-action="edit"
                    data-id="${id}">
                    <iconify-icon icon="lucide:square-pen"></iconify-icon>
                    Edit
                </button>
            `;

            if (item.status) {

                if (penggunaCount === 0) {
                    actionButtons += `
                        <button class="btn-action hapus"
                            data-action="delete"
                            data-id="${id}">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                            Hapus
                        </button>
                    `;
                } else {
                    actionButtons += `
                        <button class="btn-action nonaktif"
                            data-action="toggle"
                            data-id="${id}">
                            <iconify-icon icon="lucide:ban"></iconify-icon>
                            Nonaktif
                        </button>
                    `;
                }

            } else {

                if (penggunaCount === 0) {
                    actionButtons += `
                        <button class="btn-action hapus"
                            data-action="delete"
                            data-id="${id}">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                            Hapus
                        </button>
                    `;
                }

                actionButtons += `
                    <button class="btn-action aktif"
                        data-action="toggle"
                        data-id="${id}">
                        <iconify-icon icon="lucide:check-circle"></iconify-icon>
                        Aktifkan
                    </button>
                `;
            }

            return `
                <tr>
                    <td><strong>${kode}</strong></td>
                    <td>${nama}</td>
                    ${extraColumn}
                    <td>
                        <span class="status-user ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td>${pengguna}</td>
                    <td>
                        <div class="action-buttons">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;

        }).join("");
    }

    // Ganti tab
    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {

            tabButtons.forEach(button => {
                button.classList.remove("active");
            });

            btn.classList.add("active");

            currentTab = btn.getAttribute("data-tab");

            modalTabInput.value = currentTab;

            const url = new URL(window.location.href);
            url.searchParams.set("page", "master-data");
            url.searchParams.set("tab", currentTab);

            window.history.pushState({}, "", url);

            renderTable();
        });
    });

    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get("tab");

        if (tableHeadersMap[tab]) {
            currentTab = tab;
            modalTabInput.value = tab;

            tabButtons.forEach(btn => {
                btn.classList.toggle(
                    "active",
                    btn.getAttribute("data-tab") === tab
                );
            });

            renderTable();
        }
    });

    if (searchInput) {
        searchInput.addEventListener("input", renderTable);
    }

    // Buka modal
    function openModal(mode, id = null) {

        modalTabInput.value = currentTab;

        const config = formLabelsMap[currentTab];

        if (mode === "add") {

            modalTitle.innerText = `Tambah ${config.title}`;

            modalIdInput.value = "";
            inputKode.value = "";
            inputNama.value = "";

            if (inputExtra) {
                inputExtra.selectedIndex = 0;
            }

        } else {

            modalTitle.innerText = `Edit ${config.title}`;

            const item = (dataStore[currentTab] || [])
                .find(item => String(item.id) === String(id));

            if (!item) return;

            modalIdInput.value = item.id;
            inputKode.value = item.kode;
            inputNama.value = item.nama;

            if (currentTab === "matakuliah" && inputExtra) {
                inputExtra.value = item.jurusan || "";
            }
        }

        labelKode.innerText = config.kode;

        labelNama.innerHTML =
            `${escapeHTML(config.nama)} <span class="required">*</span>`;

        if (currentTab === "matakuliah") {
            groupExtra.classList.remove("hidden");
        } else {
            groupExtra.classList.add("hidden");
        }

        modalOverlay.classList.add("show");
    }

    function closeModal() {
        modalOverlay.classList.remove("show");
    }

    // Event tombol tambah
    if (btnTambah) {
        btnTambah.addEventListener("click", () => {
            openModal("add");
        });
    }

    if (btnCloseModal) {
        btnCloseModal.addEventListener("click", closeModal);
    }

    if (btnBatal) {
        btnBatal.addEventListener("click", closeModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener("click", event => {
            if (event.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Event tombol di tabel
    document.getElementById("table-body")
        .addEventListener("click", event => {

            const button = event.target.closest("button[data-action]");

            if (!button) return;

            const action = button.dataset.action;
            const id = button.dataset.id;

            if (action === "edit") {
                openModal("edit", id);
                return;
            }

            if (action === "toggle") {
                submitAction("toggle_status", id);
                return;
            }

            if (action === "delete") {

                if (confirm(
                    "Apakah Anda yakin ingin menghapus data ini secara permanen?"
                )) {
                    submitAction("delete", id);
                }
            }
        });

    // Kirim aksi ke Controller
    function submitAction(action, id) {

        document.getElementById("action-type").value = action;
        document.getElementById("action-tab").value = currentTab;
        document.getElementById("action-id").value = id;

        document.getElementById("action-form").submit();
    }

    // Render pertama kali
    renderTable();

});
