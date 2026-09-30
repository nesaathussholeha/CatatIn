document.addEventListener('DOMContentLoaded', function () {
    const btnMasuk = document.getElementById('btn-masuk');
    const btnDaftar = document.getElementById('btn-daftar');
    const sectionMasuk = document.getElementById('section-masuk');
    const sectionDaftar = document.getElementById('section-daftar');

    // Switcher Tab
    if (btnMasuk && btnDaftar) {
        btnMasuk.addEventListener('click', function () {
            btnMasuk.classList.add('active');
            btnDaftar.classList.remove('active');

            sectionMasuk.classList.add('active');
            sectionDaftar.classList.remove('active');
        });

        btnDaftar.addEventListener('click', function () {
            btnDaftar.classList.add('active');
            btnMasuk.classList.remove('active');

            sectionDaftar.classList.add('active');
            sectionMasuk.classList.remove('active');
        });
    }

    const regForm = document.getElementById('register-form');
    if (regForm) {
        const passwordInput = document.getElementById('reg-password');
        const konfirmasiInput = document.getElementById('reg-konfirmasi');
        const hint = document.getElementById('konfirmasi-hint');
        const submitBtn = regForm.querySelector("button[type='submit']");

        function cekKecocokanPassword() {
            if (!passwordInput || !konfirmasiInput) return true;
            const cocok = passwordInput.value === konfirmasiInput.value;
            const kosong = konfirmasiInput.value === "";

            if (!cocok && !kosong) {
                konfirmasiInput.classList.add("input-error");
                if (hint) hint.classList.add("show");
            } else {
                konfirmasiInput.classList.remove("input-error");
                if (hint) hint.classList.remove("show");
            }
            return cocok;
        }

        passwordInput.addEventListener("input", cekKecocokanPassword);
        konfirmasiInput.addEventListener("input", cekKecocokanPassword);

        regForm.addEventListener("submit", function (e) {
            if (!cekKecocokanPassword()) {
                e.preventDefault();
                konfirmasiInput.focus();
                return;
            }
            submitBtn.disabled = true;
            submitBtn.textContent = "Memproses...";
        });
    }

    // Toggle Password Visibility Universal
    const toggleBtns = document.querySelectorAll('.toggle-password-btn');

    const eyeOpenSVG = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    `;

    const eyeClosedSVG = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        </svg>
    `;

    toggleBtns.forEach(btn => {
        btn.innerHTML = eyeOpenSVG;

        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);

            if (targetInput) {
                const isPassword = targetInput.getAttribute('type') === 'password';
                targetInput.setAttribute('type', isPassword ? 'text' : 'password');
                this.innerHTML = isPassword ? eyeClosedSVG : eyeOpenSVG;
            }
        });
    });
});