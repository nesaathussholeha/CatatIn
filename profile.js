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