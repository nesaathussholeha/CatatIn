document.addEventListener('DOMContentLoaded', function () {
    // --- 1. ELEMEN TOGGLE SIDEBAR MOBILE ---
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Mencegah scroll pada background
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

    // --- 2. ELEMEN TAB NAVIGATION ---
    const tabJurusanBtn = document.getElementById('tab-jurusan-btn');
    const tabMatkulBtn = document.getElementById('tab-matkul-btn');
    const tabKategoriBtn = document.getElementById('tab-kategori-btn');

    const tabJurusanContent = document.getElementById('tab-jurusan-content');
    const tabMatkulContent = document.getElementById('tab-matkul-content');
    const tabKategoriContent = document.getElementById('tab-kategori-content');

    const tabTypeInput = document.getElementById('tab_type_input');
    const searchInput = document.getElementById('search-input');
    const mainAddForm = document.getElementById('main-add-form');
    const inputNamaBaru = document.getElementById('input-nama-baru');

    function resetTabs() {
        tabJurusanBtn.classList.remove('active');
        tabMatkulBtn.classList.remove('active');
        tabKategoriBtn.classList.remove('active');

        tabJurusanContent.classList.remove('active');
        tabMatkulContent.classList.remove('active');
        tabKategoriContent.classList.remove('active');
    }

    function switchTab(tabName, btn, content) {
        resetTabs();
        tabTypeInput.value = tabName;
        btn.classList.add('active');
        content.classList.add('active');
        searchInput.value = ''; // Reset input pencarian saat berpindah tab
        filterTable(''); 
        window.history.pushState({}, '', 'index.php?tab=' + tabName);
    }

    if (tabJurusanBtn && tabMatkulBtn && tabKategoriBtn) {
        tabJurusanBtn.addEventListener('click', () => switchTab('jurusan', tabJurusanBtn, tabJurusanContent));
        tabMatkulBtn.addEventListener('click', () => switchTab('matkul', tabMatkulBtn, tabMatkulContent));
        tabKategoriBtn.addEventListener('click', () => switchTab('kategori', tabKategoriBtn, tabKategoriContent));
    }

    // --- 3. FITUR PENCARIAN REAL-TIME ---
    function filterTable(keyword) {
        const activeTable = document.querySelector('.tab-content.active .data-table tbody');
        if (!activeTable) return;

        const rows = activeTable.querySelectorAll('tr');
        const filter = keyword.toLowerCase();

        rows.forEach(row => {
            const kode = row.querySelector('.cell-kode')?.textContent.toLowerCase() || '';
            const nama = row.querySelector('.cell-nama')?.textContent.toLowerCase() || '';

            if (kode.includes(filter) || nama.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            filterTable(this.value);
        });
    }

    // --- 4. VALIDASI FORM TAMBAH DATA ---
    if (mainAddForm) {
        mainAddForm.addEventListener('submit', function (e) {
            if (!inputNamaBaru.value.trim()) {
                e.preventDefault();
            }
        });
    }
});

function editData(type, currentName) {
    const newName = prompt(`Ubah data ${type}:`, currentName);
    if (newName !== null && newName.trim() !== '') {
        alert(`Perubahan "${newName}" siap dihubungkan ke basis data.`);
    }
}