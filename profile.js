document.addEventListener("DOMContentLoaded", function () {

    /* =====================================
       SIDEBAR RESPONSIF (HAMBURGER)
    ===================================== */

    const sidebar = document.getElementById("sidebar");
    const menuToggle = document.getElementById("menuToggle");
    const sidebarClose = document.getElementById("sidebarClose");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    if (sidebar && menuToggle && sidebarClose && sidebarOverlay) {

        function openSidebar() {
            document.body.classList.add("sidebar-open");
            menuToggle.setAttribute("aria-expanded", "true");
        }

        function closeSidebar() {
            document.body.classList.remove("sidebar-open");
            menuToggle.setAttribute("aria-expanded", "false");
        }

        menuToggle.addEventListener("click", openSidebar);
        sidebarClose.addEventListener("click", closeSidebar);
        sidebarOverlay.addEventListener("click", closeSidebar);

        // Tutup dengan tombol Esc
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") closeSidebar();
        });

        // Tutup setelah memilih menu
        sidebar.querySelectorAll(".nav-item").forEach(function (link) {
            link.addEventListener("click", closeSidebar);
        });

        // Reset kalau layar dibesarkan ke desktop
        window.addEventListener("resize", function () {
            if (window.innerWidth > 1000) closeSidebar();
        });
    }


    /* =====================================
       TAMPILKAN / SEMBUNYIKAN PASSWORD
    ===================================== */

    const passwordInput = document.getElementById("password");
    const showPassword = document.getElementById("showPassword");

    if (passwordInput && showPassword) {
        showPassword.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                showPassword.textContent = "🙈";
            } else {
                passwordInput.type = "password";
                showPassword.textContent = "👁";
            }
        });
    }


    /* =====================================
       SIMPAN PERUBAHAN
    ===================================== */

    const profileForm = document.getElementById("profileForm");
    const saveButton = document.getElementById("saveButton");

    if (profileForm && saveButton) {
        profileForm.addEventListener("submit", function () {
            saveButton.textContent = "Menyimpan...";
            saveButton.disabled = true;
        });
    }


    /* =====================================
       NONAKTIFKAN AKUN
    ===================================== */

    const deactivateButton = document.getElementById("deactivateButton");

    if (deactivateButton) {
        deactivateButton.addEventListener("click", function () {
            const confirmation = confirm(
                "Apakah kamu yakin ingin menonaktifkan akun?"
            );

            if (confirmation) {
                alert("Permintaan penonaktifan akun berhasil diproses.");
            }
        });
    }


    /* =====================================
       INTERAKSI INPUT
    ===================================== */

    const inputs = document.querySelectorAll(".form-group input");

    inputs.forEach(function (input) {
        input.addEventListener("focus", function () {
            input.style.borderColor = "#4C5FE0";
        });

        input.addEventListener("blur", function () {
            if (input.value === "") {
                input.style.borderColor = "#E1E4F2";
            }
        });
    });


    /* =====================================
       [UPLOAD] FOTO PROFIL
    ===================================== */

    const photoInput = document.getElementById("photo");
    const photoPreview = document.getElementById("photoPreview");
    const photoError = document.getElementById("photoError");

    if (photoInput && photoPreview && photoError && profileForm) {

        const allowedExt = ["jpg", "jpeg", "png"];
        const maxSize = 2 * 1024 * 1024; // 2MB

        // Validasi sisi klien (validasi final tetap di server / profile.php)
        function checkPhoto() {

            photoError.hidden = true;
            photoPreview.hidden = true;

            if (photoInput.files.length === 0) return true;

            const file = photoInput.files[0];
            const ext = file.name.split(".").pop().toLowerCase();
            let message = "";

            if (!allowedExt.includes(ext)) {
                message = "Ekstensi tidak diizinkan (hanya jpg, jpeg, png).";
            } else if (file.size > maxSize) {
                message = "Ukuran file melebihi 2MB.";
            }

            if (message) {
                photoError.textContent = message;
                photoError.hidden = false;
                photoInput.value = "";
                return false;
            }

            // Pratinjau foto sebelum diunggah
            photoPreview.src = URL.createObjectURL(file);
            photoPreview.hidden = false;

            return true;
        }

        photoInput.addEventListener("change", checkPhoto);

        // Batalkan pengiriman form jika foto tidak valid
        profileForm.addEventListener("submit", function (event) {
            if (!checkPhoto()) {
                event.preventDefault();

                if (saveButton) {
                    saveButton.textContent = "Simpan perubahan";
                    saveButton.disabled = false;
                }
            }
        });
    }

});