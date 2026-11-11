
document.addEventListener('DOMContentLoaded', function () {

    const fotoProfil = document.getElementById('fotoProfil');
    const previewFoto = document.getElementById('previewFoto');
    const avatarInitial = document.getElementById('avatarInitial');

    // Preview foto profil
    fotoProfil.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        const formatValid = ['image/jpeg', 'image/png', 'image/webp'];

        if (!formatValid.includes(file.type)) {
            alert('Format foto harus JPG, PNG, atau WEBP.');
            this.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            previewFoto.src = event.target.result;
            previewFoto.classList.add('ada-foto');
            avatarInitial.style.display = 'none';
        };

        reader.readAsDataURL(file);
    });

    // Simulasi edit username
    const formProfil = document.getElementById('formProfil');

    formProfil.addEventListener('submit', function (event) {
        event.preventDefault();

        const username = document.getElementById('username').value.trim();

        if (username.length < 3) {
            alert('Username minimal 3 karakter.');
            return;
        }

        document.getElementById('displayUsername').textContent = username;

        avatarInitial.textContent = username.charAt(0).toUpperCase();

        alert('Username berhasil diperbarui.');
    });

    // Tampilkan/sembunyikan password
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Simulasi ganti password
    const formPassword = document.getElementById('formPassword');

    formPassword.addEventListener('submit', function (event) {
        event.preventDefault();

        const passwordLama = document.getElementById('passwordLama').value;
        const passwordBaru = document.getElementById('passwordBaru').value;
        const konfirmasi = document.getElementById('konfirmasiPassword').value;

        if (passwordLama.length === 0) {
            alert('Masukkan password lama.');
            return;
        }

        if (passwordBaru.length < 8) {
            alert('Password baru minimal 8 karakter.');
            return;
        }

        if (passwordBaru !== konfirmasi) {
            alert('Konfirmasi password tidak sesuai.');
            return;
        }

        alert('Password berhasil diperbarui.');

        formPassword.reset();
    });

});
