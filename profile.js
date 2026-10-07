document.addEventListener("DOMContentLoaded", function () {

    /* =====================================
       TAMPILKAN / SEMBUNYIKAN PASSWORD
    ===================================== */

    const passwordInput =
        document.getElementById("password");

    const showPassword =
        document.getElementById("showPassword");


    showPassword.addEventListener("click", function () {

        if (passwordInput.type === "password") {

            passwordInput.type = "text";

            showPassword.textContent = "🙈";

        } else {

            passwordInput.type = "password";

            showPassword.textContent = "👁";

        }

    });


    /* =====================================
       SIMPAN PERUBAHAN
    ===================================== */

    const profileForm =
        document.getElementById("profileForm");

    const saveButton =
        document.getElementById("saveButton");


    profileForm.addEventListener("submit", function () {

        saveButton.textContent = "Menyimpan...";

        saveButton.disabled = true;

    });


    /* =====================================
       NONAKTIFKAN AKUN
    ===================================== */

    const deactivateButton =
        document.getElementById("deactivateButton");


    deactivateButton.addEventListener("click", function () {

        const confirmation = confirm(
            "Apakah kamu yakin ingin menonaktifkan akun?"
        );


        if (confirmation) {

            alert(
                "Permintaan penonaktifan akun berhasil diproses."
            );

        }

    });


    /* =====================================
       INTERAKSI INPUT
    ===================================== */

    const inputs =
        document.querySelectorAll(
            ".form-group input"
        );


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

});


/* =====================================
   [UPLOAD] FOTO PROFIL (tambahan baru)
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const photoInput = document.getElementById("photo");
    const photoPreview = document.getElementById("photoPreview");
    const photoError = document.getElementById("photoError");
    const profileForm = document.getElementById("profileForm");
    const saveButton = document.getElementById("saveButton");

    // Hentikan jika halaman ini tidak punya input foto
    if (!photoInput) return;

    const allowedExt = ["jpg", "jpeg", "png"];
    const maxSize = 2 * 1024 * 1024; // 2MB

    // Validasi di sisi klien (validasi final tetap di server / profile.php)
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

            // Kembalikan tombol simpan (sudah dinonaktifkan oleh kode di atas)
            saveButton.textContent = "Simpan perubahan";
            saveButton.disabled = false;

        }

    });

});