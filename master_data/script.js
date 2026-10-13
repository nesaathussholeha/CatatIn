document.addEventListener("DOMContentLoaded", () => {
  let currentTab = initialActiveTab || "jurusan";
  let dataStore = initialMasterData;

  // DOM Elements
  const tabButtons = document.querySelectorAll(".tab-btn");
  const searchInput = document.getElementById("search-input");
  const btnTambah = document.getElementById("btn-tambah");
  const modalOverlay = document.getElementById("modal-form");
  const btnCloseModal = document.getElementById("btn-close-modal");
  const btnBatal = document.getElementById("btn-batal");

  // Sidebar Mobile Toggle
  const hamburgerBtn = document.getElementById("hamburger-btn");
  const sidebarCloseBtn = document.getElementById("sidebar-close");
  const sidebar = document.getElementById("sidebar");
  const sidebarOverlay = document.getElementById("sidebar-overlay");

  if (hamburgerBtn) {
    hamburgerBtn.addEventListener("click", () => {
      sidebar.classList.add("active");
      sidebarOverlay.classList.add("active");
    });
  }

  const closeSidebar = () => {
    sidebar.classList.remove("active");
    sidebarOverlay.classList.remove("active");
  };

  if (sidebarCloseBtn) sidebarCloseBtn.addEventListener("click", closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener("click", closeSidebar);

  // Modal Form Elements
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
      "Aksi",
    ],
    kategori: ["Kode", "Nama Kategori", "Status", "Pengguna", "Aksi"],
  };

  // Label tanpa bintang polos
  const formLabelsMap = {
    jurusan: { title: "Jurusan", kode: "Kode", nama: "Nama Jurusan" },
    matakuliah: {
      title: "Mata Kuliah",
      kode: "Kode Mata Kuliah",
      nama: "Nama Mata Kuliah",
    },
    kategori: {
      title: "Kategori Dokumen",
      kode: "Kode Kategori",
      nama: "Nama Kategori",
    },
  };

  renderTable();

  tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      currentTab = btn.getAttribute("data-tab");
      modalTabInput.value = currentTab;

      window.history.pushState({}, "", `index.php?tab=${currentTab}`);
      renderTable();
    });
  });

  searchInput.addEventListener("input", renderTable);

  function renderTable() {
    const tableHeaders = document.getElementById("table-headers");
    const tableBody = document.getElementById("table-body");
    const query = searchInput.value.toLowerCase();

    tableHeaders.innerHTML = tableHeadersMap[currentTab]
      .map((h) => `<th>${h}</th>`)
      .join("");

    const items = dataStore[currentTab] || [];
    const filtered = items.filter((item) => {
      return (
        (item.kode && item.kode.toLowerCase().includes(query)) ||
        (item.nama && item.nama.toLowerCase().includes(query)) ||
        (item.jurusan && item.jurusan.toLowerCase().includes(query))
      );
    });

    if (filtered.length === 0) {
      tableBody.innerHTML = `
                <tr>
                    <td colspan="${tableHeadersMap[currentTab].length}" style="text-align:center; padding: 32px; color: #71809d;">
                        Tidak ada data ditemukan.
                    </td>
                </tr>`;
      return;
    }

    tableBody.innerHTML = filtered
      .map((item) => {
        const statusClass = item.status ? "status-active" : "status-blocked";
        const statusText = item.status ? "Aktif" : "Non-Aktif";

        const penggunaCount = parseInt(item.pengguna) || 0;

        let extraColumn = "";
        if (currentTab === "matakuliah") {
          extraColumn = `<td>${item.jurusan || "-"}</td>`;
        }

        let actionButtonsHTML = "";

        if (item.status) {
          if (penggunaCount === 0) {
            actionButtonsHTML = `
                        <button class="btn-action edit" onclick="openEditModal('${item.id}')">
                            <iconify-icon icon="lucide:square-pen"></iconify-icon> Edit
                        </button>
                        <button class="btn-action hapus" onclick="triggerDelete('${item.id}')">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus
                        </button>
                    `;
          } else {
            actionButtonsHTML = `
                        <button class="btn-action edit" onclick="openEditModal('${item.id}')">
                            <iconify-icon icon="lucide:square-pen"></iconify-icon> Edit
                        </button>
                        <button class="btn-action nonaktif" onclick="triggerToggleStatus('${item.id}')">
                            <iconify-icon icon="lucide:ban"></iconify-icon> Nonaktif
                        </button>
                    `;
          }
        } else {
          if (penggunaCount === 0) {
            actionButtonsHTML = `
                        <button class="btn-action edit" onclick="openEditModal('${item.id}')">
                            <iconify-icon icon="lucide:square-pen"></iconify-icon> Edit
                        </button>
                        <button class="btn-action hapus" onclick="triggerDelete('${item.id}')">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus
                        </button>
                        <button class="btn-action aktif" onclick="triggerToggleStatus('${item.id}')">
                            <iconify-icon icon="lucide:check-circle"></iconify-icon> Aktifkan
                        </button>
                    `;
          } else {
            actionButtonsHTML = `
                        <button class="btn-action edit" onclick="openEditModal('${item.id}')">
                            <iconify-icon icon="lucide:square-pen"></iconify-icon> Edit
                        </button>
                        <button class="btn-action aktif" onclick="triggerToggleStatus('${item.id}')">
                            <iconify-icon icon="lucide:check-circle"></iconify-icon> Aktifkan
                        </button>
                    `;
          }
        }

        return `
                <tr>
                    <td><strong>${item.kode}</strong></td>
                    <td>${item.nama}</td>
                    ${extraColumn}
                    <td><span class="status-user ${statusClass}">${statusText}</span></td>
                    <td>${item.pengguna}</td>
                    <td>
                        <div class="action-buttons">
                            ${actionButtonsHTML}
                        </div>
                    </td>
                </tr>
            `;
      })
      .join("");
  }

  btnTambah.addEventListener("click", () => openModal("add"));
  btnCloseModal.addEventListener("click", closeModal);
  btnBatal.addEventListener("click", closeModal);

  modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) closeModal();
  });

  function openModal(mode, id = null) {
    modalTabInput.value = currentTab;
    const config = formLabelsMap[currentTab];

    if (mode === "add") {
      modalTitle.innerText = `Tambah ${config.title}`;
      modalIdInput.value = "";
      inputKode.value = "";
      inputNama.value = "";
    } else {
      modalTitle.innerText = `Edit ${config.title}`;
      const item = dataStore[currentTab].find((i) => i.id === id);
      if (item) {
        modalIdInput.value = item.id;
        inputKode.value = item.kode;
        inputNama.value = item.nama;
        if (currentTab === "matakuliah" && item.jurusan) {
          inputExtra.value = item.jurusan;
        }
      }
    }

    labelKode.innerText = config.kode;
    // Bintang disisipkan kembali beserta tag span class required
    labelNama.innerHTML = `${config.nama} <span class="required">*</span>`;

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

  window.openEditModal = function (id) {
    openModal("edit", id);
  };

  window.triggerToggleStatus = function (id) {
    document.getElementById("action-type").value = "toggle_status";
    document.getElementById("action-tab").value = currentTab;
    document.getElementById("action-id").value = id;
    document.getElementById("action-form").submit();
  };

  window.triggerDelete = function (id) {
    if (
      confirm("Apakah Anda yakin ingin menghapus data ini secara permanen?")
    ) {
      document.getElementById("action-type").value = "delete";
      document.getElementById("action-tab").value = currentTab;
      document.getElementById("action-id").value = id;
      document.getElementById("action-form").submit();
    }
  };
});
