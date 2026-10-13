document.addEventListener("DOMContentLoaded", () => {

    const menuToggle = document.getElementById("menuToggle");
    const closeSidebar = document.getElementById("closeSidebar");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    const openMenu = () => {
        sidebar.classList.add("active");
        overlay.classList.add("active");
    };

    const closeMenu = () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    };

    if (menuToggle) menuToggle.addEventListener("click", openMenu);
    if (closeSidebar) closeSidebar.addEventListener("click", closeMenu);
    if (overlay) overlay.addEventListener("click", closeMenu);

    /* ---------------- Konfigurasi ---------------- */
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    const ALLOWED_EXT = ["pdf", "doc", "docx", "xls", "xlsx"];
    const TOTAL_PAGES = document.querySelectorAll(".paper-page").length;

    /* ---------------- STATE ---------------- */
    const state = {
        modalOpen: false,
        submitting: false,
        form: {
            judul: "",
            jenis: "File Rusak", 
            deskripsi: "",
            file: null,
        },
        errors: {},
        previewPage: 1,
    };

    /* ---------------- Elemen ---------------- */
    const $ = (id) => document.getElementById(id);

    const modal = $("modalLaporan");
    const form = $("formLaporan");
    const inputJudul = $("inputJudul");
    const inputDeskripsi = $("inputDeskripsi");
    const inputFile = $("inputFile");
    const counterJudul = $("counterJudul");
    const counterDeskripsi = $("counterDeskripsi");
    const chipGroup = $("chipGroup");
    const dropzone = $("dropzone");
    const filePreview = $("filePreview");
    const fileName = $("fileName");
    const fileSize = $("fileSize");
    const btnKirim = $("btnKirim");
    const toastStack = $("toastStack");

    let lastFocused = null;

    function renderModal() {
        modal.classList.toggle("open", state.modalOpen);
        modal.setAttribute("aria-hidden", String(!state.modalOpen));
        document.body.classList.toggle("modal-open", state.modalOpen);
    }

    function renderCounters() {
        counterJudul.textContent = `${state.form.judul.length}/100`;
        counterDeskripsi.textContent = `${state.form.deskripsi.length}/500`;
        counterJudul.classList.toggle("near-limit", state.form.judul.length >= 90);
        counterDeskripsi.classList.toggle("near-limit", state.form.deskripsi.length >= 450);
    }

    function renderChips() {
        chipGroup.querySelectorAll(".chip").forEach((chip) => {
            const active = chip.dataset.value === state.form.jenis;
            chip.classList.toggle("selected", active);
            chip.setAttribute("aria-checked", String(active));
        });
    }

    function renderFile() {
        const f = state.form.file;
        dropzone.hidden = !!f;
        filePreview.hidden = !f;
        if (f) {
            fileName.textContent = f.name;
            fileSize.textContent = formatSize(f.size);
        }
    }

    function renderErrors() {
        ["judul", "jenis", "deskripsi", "file"].forEach((key) => {
            const wrap = document.querySelector(`.field[data-field="${key}"]`);
            const text = document.querySelector(`[data-error="${key}"]`);
            const msg = state.errors[key] || "";
            wrap.classList.toggle("has-error", !!msg);
            text.textContent = msg;
        });
    }

    function renderSubmitting() {
        btnKirim.disabled = state.submitting;
        btnKirim.classList.toggle("loading", state.submitting);
        btnKirim.querySelector(".btn-label").textContent = state.submitting
            ? "Mengirim..."
            : "Kirim Laporan";
        $("btnBatal").disabled = state.submitting;
    }

    function renderAll() {
        renderModal();
        renderCounters();
        renderChips();
        renderFile();
        renderErrors();
        renderSubmitting();
    }

    function formatSize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
    }

    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `toast ${type}`;
        const icon = type === "success" ? "lucide:check-circle-2" : "lucide:alert-circle";
        toast.innerHTML = `<iconify-icon icon="${icon}"></iconify-icon><span></span>`;
        toast.querySelector("span").textContent = message;
        toastStack.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("hide");
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    function validateFile(file) {
        const ext = file.name.split(".").pop().toLowerCase();
        if (!ALLOWED_EXT.includes(ext)) return "Format file tidak didukung. Gunakan PDF, DOC, DOCX, atau XLS.";
        if (file.size > MAX_FILE_SIZE) return "Ukuran file maksimal 10 MB.";
        return "";
    }

    function validateForm() {
        const errors = {};
        const judul = state.form.judul.trim();

        if (!judul) errors.judul = "Judul laporan wajib diisi.";
        else if (judul.length > 100) errors.judul = "Judul maksimal 100 karakter.";

        if (!state.form.jenis) errors.jenis = "Pilih salah satu jenis laporan.";

        if (state.form.deskripsi.length > 500) errors.deskripsi = "Deskripsi maksimal 500 karakter.";

        if (state.form.file) {
            const msg = validateFile(state.form.file);
            if (msg) errors.file = msg;
        }
        return errors;
    }

    function resetForm() {
        state.form = { judul: "", jenis: "File Rusak", deskripsi: "", file: null };
        state.errors = {};
        form.reset();
        renderAll();
    }

    function openModal() {
        lastFocused = document.activeElement;
        state.modalOpen = true;
        renderModal();
        setTimeout(() => inputJudul.focus(), 150);
    }

    function closeModal(force = false) {
        if (state.submitting && !force) return;
        state.modalOpen = false;
        renderModal();
        if (lastFocused) lastFocused.focus();
    }

    $("btnLaporkan").addEventListener("click", openModal);
    $("btnLaporkanBawah").addEventListener("click", openModal);
    $("modalClose").addEventListener("click", () => closeModal());
    $("btnBatal").addEventListener("click", () => {
        closeModal();
    });

    // Klik area gelap menutup modal
    modal.addEventListener("mousedown", (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener("keydown", (e) => {
        if (!state.modalOpen) return;

        if (e.key === "Escape") closeModal();

        // Focus trap sederhana
        if (e.key === "Tab") {
            const focusables = modal.querySelectorAll(
                'button:not([disabled]), input:not([hidden]):not([disabled]), textarea, [tabindex="0"]'
            );
            const visible = [...focusables].filter((el) => el.offsetParent !== null);
            if (!visible.length) return;
            const first = visible[0];
            const last = visible[visible.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    inputJudul.addEventListener("input", () => {
        state.form.judul = inputJudul.value;
        delete state.errors.judul;
        renderCounters();
        renderErrors();
    });

    inputDeskripsi.addEventListener("input", () => {
        state.form.deskripsi = inputDeskripsi.value;
        delete state.errors.deskripsi;
        renderCounters();
        renderErrors();
    });

    chipGroup.addEventListener("click", (e) => {
        const chip = e.target.closest(".chip");
        if (!chip) return;
        state.form.jenis = chip.dataset.value;
        delete state.errors.jenis;
        renderChips();
        renderErrors();
    });

    /* ---------------- Upload file ---------------- */
    function setFile(file) {
        if (!file) return;
        const msg = validateFile(file);
        if (msg) {
            state.errors.file = msg;
            state.form.file = null;
            inputFile.value = "";
        } else {
            delete state.errors.file;
            state.form.file = file;
        }
        renderFile();
        renderErrors();
    }

    dropzone.addEventListener("click", () => inputFile.click());
    dropzone.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            inputFile.click();
        }
    });

    inputFile.addEventListener("change", () => setFile(inputFile.files[0]));

    ["dragenter", "dragover"].forEach((evt) =>
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.add("dragover");
        })
    );

    ["dragleave", "drop"].forEach((evt) =>
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.remove("dragover");
        })
    );

    dropzone.addEventListener("drop", (e) => {
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            inputFile.files = dt.files;
            setFile(file);
        }
    });

    $("fileRemove").addEventListener("click", () => {
        state.form.file = null;
        inputFile.value = "";
        delete state.errors.file;
        renderFile();
        renderErrors();
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (state.submitting) return;

        state.errors = validateForm();
        renderErrors();

        if (Object.keys(state.errors).length) {
            // fokus ke field error pertama
            const first = document.querySelector(".field.has-error input, .field.has-error textarea, .field.has-error .chip");
            if (first) first.focus();
            return;
        }

        state.submitting = true;
        renderSubmitting();

        const data = new FormData();
        data.append("catatan_id", form.elements["catatan_id"].value);
        data.append("judul", state.form.judul.trim());
        data.append("jenis", state.form.jenis);
        data.append("deskripsi", state.form.deskripsi.trim());
        if (state.form.file) data.append("file", state.form.file);

        try {
            const res = await fetch("detail-catatan.php?aksi=kirim-laporan", {
                method: "POST",
                body: data,
            });
            const json = await res.json();

            if (res.ok && json.success) {
                state.submitting = false;
                closeModal(true);
                resetForm();
                showToast(json.message || "Laporan berhasil dikirim.", "success");
            } else {
                state.errors = json.errors || {};
                renderErrors();
                showToast("Periksa kembali isian laporan Anda.", "error");
            }
        } catch (err) {
            showToast("Terjadi kesalahan jaringan. Coba lagi.", "error");
        } finally {
            state.submitting = false;
            renderSubmitting();
        }
    });

    const pages = document.querySelectorAll(".paper-page");
    const pageNow = $("pageNow");
    const pagePrev = $("pagePrev");
    const pageNext = $("pageNext");
    const pageNums = document.querySelectorAll(".pager-num");

    function renderPreview() {
        pages.forEach((p) => p.classList.toggle("active", Number(p.dataset.page) === state.previewPage));
        pageNums.forEach((b) => b.classList.toggle("active", Number(b.dataset.goto) === state.previewPage));
        pageNow.textContent = state.previewPage;
        pagePrev.disabled = state.previewPage === 1;
        pageNext.disabled = state.previewPage === TOTAL_PAGES;
    }

    function goToPage(n) {
        state.previewPage = Math.min(Math.max(n, 1), TOTAL_PAGES);
        renderPreview();
    }

    pagePrev.addEventListener("click", () => goToPage(state.previewPage - 1));
    pageNext.addEventListener("click", () => goToPage(state.previewPage + 1));
    pageNums.forEach((b) => b.addEventListener("click", () => goToPage(Number(b.dataset.goto))));

    $("btnUnduh").addEventListener("click", () => {
        showToast("Unduhan catatan dimulai...", "success");
    });

    /* ---------------- Init ---------------- */
    renderAll();
    renderPreview();
});