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
});

document.addEventListener('DOMContentLoaded', function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
    var submitting = false;

    /* ---------- Hitung naik angka statistik ---------- */
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduce) {
        $$('.num[data-count]').forEach(function (el) {
            var target = parseInt(el.dataset.count, 10), start = null, dur = 900;
            function step(t) {
                if (start === null) start = t;
                var p = Math.min((t - start) / dur, 1);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(step);
            }
            el.textContent = '0';
            requestAnimationFrame(step);
        });
    }

    /* ---------- Tampilkan / sembunyikan password ---------- */
    var EYE = '<svg class="ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
    var EYE_OFF = '<svg class="ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18M10.6 5.1A9.7 9.7 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.7 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 4.4-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
    $$('.eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.innerHTML = show ? EYE : EYE_OFF;
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    /* ---------- Aturan validasi per kolom ---------- */
    var rules = {
        nama:    function (v) { return v.trim().length >= 2 || 'Nama lengkap minimal 2 karakter.'; },
        jurusan: function (v) { return v.trim() !== '' || 'Jurusan wajib diisi.'; },
        nim:     function (v) { return /^[0-9]{5,20}$/.test(v) || 'NIM hanya berisi angka (5–20 digit).'; },
        email:   function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) || 'Format email belum benar.'; },
        hp:      function (v) { return /^(\+62|0)[0-9]{8,13}$/.test(v.trim()) || 'Nomor HP harus diawali 0 atau +62 (9–14 digit).'; },
        pl:      function (v) { return v !== '' || 'Password saat ini wajib diisi.'; },
        pb:      function (v) { return v.length >= 8 || 'Password baru minimal 8 karakter.'; },
        pk:      function (v) { return (v !== '' && v === $('#pb').value) || 'Konfirmasi tidak sama dengan password baru.'; }
    };
    var okText = { pk: 'Password cocok.' };

    /* State kolom: netral / valid / error */
    function clearState(input) {
        var wrap = input.closest('.field');
        $$('.err, .ok-msg', wrap).forEach(function (n) { n.remove(); });
        input.classList.remove('invalid', 'valid');
        input.removeAttribute('aria-invalid');
    }
    function setState(input, result) {
        clearState(input);
        var ok = result === true;
        input.classList.add(ok ? 'valid' : 'invalid');
        input.setAttribute('aria-invalid', ok ? 'false' : 'true');
        var text = ok ? okText[input.id] : result;
        if (text) {
            var p = document.createElement('p');
            p.className = ok ? 'ok-msg' : 'err';
            p.textContent = text;
            input.closest('.field').appendChild(p);
        }
    }
    function live(input) {
        var r = rules[input.id](input.value);
        if (r === true && input.id === 'pl') clearState(input); 
        else setState(input, r);
        return r === true;
    }

    /* Validasi muncul setelah kolom disentuh (blur), lalu ikut berubah saat mengetik */
    $$('#formProfil input[type="text"], #formProfil input[type="email"], #formProfil input[type="tel"], #formPass input').forEach(function (input) {
        if (input.classList.contains('invalid')) input.dataset.touched = '1'; 
        input.addEventListener('blur', function () {
            if (input.value === '' && !input.dataset.touched) return;
            input.dataset.touched = '1';
            live(input);
        });
        input.addEventListener('input', function () {
            if (input.dataset.touched) live(input); else clearState(input);
        });
    });

    /* NIM hanya angka; HP angka dengan + di awal */
    $('#nim').addEventListener('input', function (e) { e.target.value = e.target.value.replace(/\D/g, ''); });
    $('#hp').addEventListener('input', function (e) { e.target.value = e.target.value.replace(/(?!^\+)\D/g, ''); });

    /* ---------- Pratinjau nama & jurusan langsung ---------- */
    $('#nama').addEventListener('input', function (e) { $('#liveName').textContent = e.target.value || 'Nama lengkap'; });
    $('#jurusan').addEventListener('input', function (e) { $('#liveJurusan').textContent = e.target.value; });

    /* ---------- State form profil: tombol aktif hanya jika ada perubahan ---------- */
    var formP = $('#formProfil'), btnS = $('#btnSimpan'), dirtyNote = $('#dirtyNote');
    var fotoInput = $('#foto'), fotoImg = $('#photoImg'), fotoDefault = $('#photoDefault');
    var fields = ['nama', 'jurusan', 'nim', 'email', 'hp'];

    function snapshot() {
        var f = fotoInput.files[0];
        return fields.map(function (id) { return $('#' + id).value; }).join('\u0001') + '\u0001' + (f ? f.name + f.size : '');
    }
    var initial = snapshot();
    var hasServerError = !!$('.err', formP);
    function refreshProfil() {
        var dirty = hasServerError || snapshot() !== initial;
        btnS.disabled = !dirty;
        dirtyNote.hidden = !dirty;
    }
    fields.forEach(function (id) { $('#' + id).addEventListener('input', refreshProfil); });
    refreshProfil();

    /* ---------- Ganti foto + pratinjau ---------- */
    $('#btnFoto').addEventListener('click', function () { fotoInput.click(); });
    fotoInput.addEventListener('change', function () {
        var file = fotoInput.files[0];
        if (!file) { refreshProfil(); return; }
        var okType = ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) > -1;
        if (!okType || file.size > 2 * 1024 * 1024) {
            alert(!okType ? 'Gunakan foto berformat JPG, PNG, atau WEBP.' : 'Ukuran foto maksimal 2 MB.');
            fotoInput.value = '';
            refreshProfil();
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            fotoImg.src = e.target.result;
            fotoImg.hidden = false;
            if (fotoDefault) fotoDefault.hidden = true;
        };
        reader.readAsDataURL(file);
        refreshProfil();
    });

    /* ---------- State form password: tombol aktif jika ketiga kolom terisi ---------- */
    var formW = $('#formPass'), btnW = $('#btnPassword');
    var pl = $('#pl'), pb = $('#pb'), pk = $('#pk');
    function refreshPassword() { btnW.disabled = !(pl.value && pb.value && pk.value); }
    [pl, pb, pk].forEach(function (i) { i.addEventListener('input', refreshPassword); });
    pb.addEventListener('input', function () { if (pk.dataset.touched) live(pk); }); // cocok/tidak ikut diperbarui
    refreshPassword();

    /* ---------- Kekuatan password ---------- */
    var meter = $('#meter'), meterLabel = $('#meterLabel');
    var labels = ['', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
    pb.addEventListener('input', function () {
        var v = pb.value, score = 0;
        if (v.length >= 8) score++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        meter.classList.toggle('on', v.length > 0);
        meter.dataset.level = v ? Math.max(score, 1) : 0;
        meterLabel.textContent = v ? 'Kekuatan password: ' + labels[Math.max(score, 1)] : '';
    });

    /* ---------- Kirim form: validasi semua + state loading ---------- */
    function bindSubmit(form, ids, btn, loadingText) {
        form.addEventListener('submit', function (e) {
            var first = null;
            ids.forEach(function (id) {
                var el = $('#' + id);
                el.dataset.touched = '1';
                if (!live(el) && !first) first = el;
            });
            if (first) { e.preventDefault(); first.focus(); return; }

            submitting = true;
            btn.dataset.html = btn.innerHTML;
            btn.innerHTML = '<span class="spin" aria-hidden="true"></span>' + loadingText;
            btn.classList.add('is-loading');
            setTimeout(function () { btn.disabled = true; }, 0);
        });
    }
    bindSubmit(formP, fields, btnS, 'Menyimpan...');
    bindSubmit(formW, ['pl', 'pb', 'pk'], btnW, 'Mengubah...');

    /* Peringatan jika meninggalkan halaman dengan perubahan belum disimpan */
    window.addEventListener('beforeunload', function (e) {
        var dirty = !btnS.disabled || pl.value || pb.value || pk.value;
        if (!submitting && dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    /* Kembali lewat tombol Back: pulihkan tombol dari state loading */
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        submitting = false;
        [btnS, btnW].forEach(function (b) {
            if (b.dataset.html) { b.innerHTML = b.dataset.html; b.classList.remove('is-loading'); delete b.dataset.html; }
        });
        refreshProfil();
        refreshPassword();
    });

    /* ---------- Notifikasi hilang otomatis ---------- */
    var toast = $('#toast');
    if (toast) {
        setTimeout(function () {
            toast.classList.add('hide');
            setTimeout(function () { toast.remove(); }, 450);
        }, 4500);
    }
});