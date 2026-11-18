
document.addEventListener('DOMContentLoaded', function () {
    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) =>
        Array.from(root.querySelectorAll(selector));

    const formProfil = $('#formProfil');
    const formPass = $('#formPass');

    if (!formProfil || !formPass) return;

    let submitting = false;

    // Animasi statistik
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    if (!reduceMotion) {
        $$('.num[data-count]').forEach(function (element) {
            const target = parseInt(element.dataset.count, 10);
            let start = null;
            const duration = 900;

            function animate(timestamp) {
                if (start === null) start = timestamp;

                const progress = Math.min(
                    (timestamp - start) / duration,
                    1
                );

                element.textContent = Math.round(
                    target * (1 - Math.pow(1 - progress, 3))
                );

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }

            element.textContent = '0';
            requestAnimationFrame(animate);
        });
    }

    // Tampilkan / sembunyikan password
    $$('.eye').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);

            if (!input) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            button.textContent = show ? '◉' : '◉';
            button.setAttribute(
                'aria-label',
                show ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    });

    // Elemen form profil
    const nama = $('#nama');
    const jurusan = $('#jurusan');
    const nim = $('#nim');
    const email = $('#email');
    const hp = $('#hp');

    const fotoInput = $('#foto');
    const fotoImg = $('#photoImg');
    const fotoDefault = $('#photoDefault');

    const btnFoto = $('#btnFoto');
    const btnSimpan = $('#btnSimpan');
    const dirtyNote = $('#dirtyNote');

    const profileFields = [nama, jurusan, nim, email, hp];

    // Aturan validasi
    const rules = {
        nama: value =>
            value.trim().length >= 2 ||
            'Nama lengkap minimal 2 karakter.',

        jurusan: value =>
            value.trim() !== '' ||
            'Jurusan wajib diisi.',

        nim: value =>
            /^[0-9]{5,20}$/.test(value) ||
            'NIM hanya berisi angka (5–20 digit).',

        email: value =>
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()) ||
            'Format email belum benar.',

        hp: value =>
            /^(\+62|0)[0-9]{8,13}$/.test(value.trim()) ||
            'Nomor HP harus diawali 0 atau +62.'
    };

    function clearState(input) {
        if (!input) return;

        const wrapper = input.closest('.field');
        if (!wrapper) return;

        $$('.err, .ok-msg', wrapper).forEach(el => el.remove());

        input.classList.remove('invalid', 'valid');
        input.removeAttribute('aria-invalid');
    }

    function setState(input, result) {
        clearState(input);

        const valid = result === true;

        input.classList.add(valid ? 'valid' : 'invalid');
        input.setAttribute('aria-invalid', valid ? 'false' : 'true');

        if (valid) return true;

        const message = document.createElement('p');
        message.className = 'err';
        message.textContent = result;

        input.closest('.field').appendChild(message);

        return false;
    }

    function validate(input) {
        if (!input || !rules[input.id]) return true;

        return setState(input, rules[input.id](input.value));
    }

    // Validasi ketika kolom disentuh
    profileFields.forEach(function (input) {
        input.addEventListener('blur', function () {
            input.dataset.touched = '1';
            validate(input);
        });

        input.addEventListener('input', function () {
            if (input.dataset.touched) {
                validate(input);
            } else {
                clearState(input);
            }
        });
    });

    // NIM hanya angka
    nim.addEventListener('input', function () {
        nim.value = nim.value.replace(/\D/g, '');
    });

    // HP hanya angka, kecuali tanda + di awal
    hp.addEventListener('input', function () {
        hp.value = hp.value.replace(/(?!^\+)\D/g, '');
    });

    // Preview nama dan jurusan
    nama.addEventListener('input', function () {
        $('#liveName').textContent = nama.value || 'Nama lengkap';
    });

    jurusan.addEventListener('input', function () {
        $('#liveJurusan').textContent = jurusan.value;
    });

    // Deteksi perubahan form profil
    function snapshot() {
        const file = fotoInput.files[0];

        return profileFields.map(input => input.value).join('\u0001') +
            '\u0001' +
            (file ? file.name + file.size : '');
    }

    const initialSnapshot = snapshot();

    function refreshProfil() {
        const changed = snapshot() !== initialSnapshot;

        btnSimpan.disabled = !changed;
        dirtyNote.hidden = !changed;
    }

    profileFields.forEach(input => {
        input.addEventListener('input', refreshProfil);
    });

    // Tombol ganti foto
    btnFoto.addEventListener('click', function () {
        fotoInput.click();
    });

    // Preview foto
    fotoInput.addEventListener('change', function () {
        const file = fotoInput.files[0];

        if (!file) {
            refreshProfil();
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Gunakan foto berformat JPG, PNG, atau WEBP.');
            fotoInput.value = '';
            refreshProfil();
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            fotoInput.value = '';
            refreshProfil();
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            fotoImg.src = event.target.result;
            fotoImg.hidden = false;
            fotoDefault.hidden = true;
        };

        reader.readAsDataURL(file);
        refreshProfil();
    });

    // Elemen form password
    const passwordLama = $('#pl');
    const passwordBaru = $('#pb');
    const passwordKonfirmasi = $('#pk');

    const btnPassword = $('#btnPassword');

    const meter = $('#meter');
    const meterLabel = $('#meterLabel');

    // Validasi password
    function validatePassword() {
        let valid = true;

        const checks = [
            {
                input: passwordLama,
                result: passwordLama.value !== '' ||
                    'Password saat ini wajib diisi.'
            },
            {
                input: passwordBaru,
                result: passwordBaru.value.length >= 8 ||
                    'Password baru minimal 8 karakter.'
            },
            {
                input: passwordKonfirmasi,
                result: (
                    passwordKonfirmasi.value !== '' &&
                    passwordKonfirmasi.value === passwordBaru.value
                ) || 'Konfirmasi tidak sama dengan password baru.'
            }
        ];

        checks.forEach(function (check) {
            if (!setState(check.input, check.result)) {
                valid = false;
            }
        });

        return valid;
    }

    function refreshPassword() {
        btnPassword.disabled = !(
            passwordLama.value &&
            passwordBaru.value &&
            passwordKonfirmasi.value
        );
    }

    [passwordLama, passwordBaru, passwordKonfirmasi].forEach(input => {
        input.addEventListener('input', function () {
            input.dataset.touched = '1';

            if (input === passwordKonfirmasi ||
                input === passwordBaru) {
                if (passwordKonfirmasi.dataset.touched) {
                    setState(
                        passwordKonfirmasi,
                        passwordKonfirmasi.value !== '' &&
                        passwordKonfirmasi.value === passwordBaru.value
                            ? true
                            : 'Konfirmasi tidak sama dengan password baru.'
                    );
                }
            }

            refreshPassword();
        });

        input.addEventListener('blur', function () {
            input.dataset.touched = '1';
            validatePassword();
        });
    });

    // Kekuatan password
    const strengthLabels = [
        '',
        'Lemah',
        'Cukup',
        'Bagus',
        'Kuat'
    ];

    passwordBaru.addEventListener('input', function () {
        const value = passwordBaru.value;
        let score = 0;

        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const level = value ? Math.max(score, 1) : 0;

        meter.classList.toggle('on', value.length > 0);
        meter.dataset.level = level;

        meterLabel.textContent = value
            ? 'Kekuatan password: ' + strengthLabels[level]
            : '';
    });

    // Submit profil
    formProfil.addEventListener('submit', function (event) {
        let firstInvalid = null;

        profileFields.forEach(function (input) {
            input.dataset.touched = '1';

            if (!validate(input) && !firstInvalid) {
                firstInvalid = input;
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
            return;
        }

        submitting = true;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML =
            '<span class="spin"></span> Menyimpan...';
        btnSimpan.classList.add('is-loading');
    });

    // Submit password
    formPass.addEventListener('submit', function (event) {
        if (!validatePassword()) {
            event.preventDefault();

            const invalid = $('.invalid', formPass);
            if (invalid) invalid.focus();

            return;
        }

        submitting = true;
        btnPassword.disabled = true;
        btnPassword.innerHTML =
            '<span class="spin"></span> Mengubah...';
        btnPassword.classList.add('is-loading');
    });

    // Peringatan jika meninggalkan halaman dengan perubahan
    window.addEventListener('beforeunload', function (event) {
        const profileChanged = !btnSimpan.disabled;

        const passwordChanged =
            passwordLama.value ||
            passwordBaru.value ||
            passwordKonfirmasi.value;

        if (!submitting && (profileChanged || passwordChanged)) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    // Notifikasi hilang otomatis
    const toast = $('#toast');

    if (toast) {
        setTimeout(function () {
            toast.classList.add('hide');

            setTimeout(function () {
                toast.remove();
            }, 450);
        }, 4500);
    }

    refreshProfil();
    refreshPassword();
});
