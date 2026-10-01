document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('overlay');
    const form = document.getElementById('formLaporan');
    const btnBuka = document.getElementById('btnBuka');
    const btnTutup = document.getElementById('btnTutup');
    const btnBatal = document.getElementById('btnBatal');
    const judul = document.getElementById('judul');

    const bukaModal = () => {
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(() => judul.focus(), 50);
    };
    const tutupModal = () => {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    };

    btnBuka.addEventListener('click', bukaModal);
    btnTutup.addEventListener('click', tutupModal);
    btnBatal.addEventListener('click', () => {
        form.reset();
        resetFile();
        updateCounters();
        tutupModal();
    });
    overlay.addEventListener('click', (e) => { if (e.target === overlay) tutupModal(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('show')) tutupModal();
    });
    if (overlay.classList.contains('show')) document.body.style.overflow = 'hidden';

    // ---- Penghitung karakter ----
    const counters = document.querySelectorAll('.count');
    function updateCounters() {
        counters.forEach((c) => {
            const el = document.getElementById(c.dataset.for);
            c.textContent = `${el.value.length}/${c.dataset.max}`;
        });
    }
    counters.forEach((c) => document.getElementById(c.dataset.for).addEventListener('input', updateCounters));
    updateCounters();

    // ---- Unggah file (klik + drag & drop) ----
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file');
    const dzText = document.getElementById('dzText');
    const fileError = document.getElementById('fileError');
    const teksAwal = dzText.textContent;
    const extOk = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    const MAX = 10 * 1024 * 1024;

    function resetFile() {
        fileInput.value = '';
        dzText.textContent = teksAwal;
        fileError.textContent = '';
    }

    function cekFile(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (!extOk.includes(ext)) return 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.';
        if (file.size > MAX) return 'Ukuran file maksimal 10 MB.';
        return '';
    }

    function tampilFile() {
        const file = fileInput.files[0];
        if (!file) return resetFile();
        const err = cekFile(file);
        if (err) {
            fileInput.value = '';
            dzText.textContent = teksAwal;
            fileError.textContent = err;
            return;
        }
        fileError.textContent = '';
        dzText.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
    }

    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); }
    });
    fileInput.addEventListener('change', tampilFile);

    ['dragenter', 'dragover'].forEach((ev) =>
        dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('drag'); }));
    ['dragleave', 'drop'].forEach((ev) =>
        dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('drag'); }));
    dropzone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            tampilFile();
        }
    });

    // ---- Validasi sebelum kirim ----
    form.addEventListener('submit', (e) => {
        const errJudul = judul.parentElement.querySelector('.error');
        if (!judul.value.trim()) {
            e.preventDefault();
            judul.classList.add('invalid');
            errJudul.textContent = 'Judul laporan wajib diisi.';
            judul.focus();
        }
    });
    judul.addEventListener('input', () => {
        judul.classList.remove('invalid');
        judul.parentElement.querySelector('.error').textContent = '';
    });

    // Sembunyikan notifikasi sukses otomatis
    const alertBox = document.getElementById('alert');
    if (alertBox) setTimeout(() => alertBox.remove(), 4000);
});