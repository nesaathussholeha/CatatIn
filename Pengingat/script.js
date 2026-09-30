document.addEventListener('DOMContentLoaded', function () {
    const modal      = document.getElementById('modalTugas');
    const modalJudul = document.getElementById('modalJudul');
    const inputAksi  = document.getElementById('inputAksi');
    const inputId    = document.getElementById('inputId');
    const inputNama  = document.getElementById('inputJudul');
    const inputTgl   = document.getElementById('inputDeadline');

    function bukaModal() {
        modal.classList.add('tampil');
        modal.setAttribute('aria-hidden', 'false');
        inputNama.focus();
    }

    function tutupModal() {
        modal.classList.remove('tampil');
        modal.setAttribute('aria-hidden', 'true');
    }

    // Tombol "+ Tambah tugas"
    document.getElementById('btnTambah').addEventListener('click', function () {
        modalJudul.textContent = 'Tambah tugas';
        inputAksi.value = 'tambah';
        inputId.value   = '';
        inputNama.value = '';
        inputTgl.value  = '';
        bukaModal();
    });

    // Tombol "Edit" (isi form pakai data dari atribut data-*)
    document.querySelectorAll('.btn-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            modalJudul.textContent = 'Edit tugas';
            inputAksi.value = 'edit';
            inputId.value   = btn.dataset.id;
            inputNama.value = btn.dataset.judul;
            inputTgl.value  = btn.dataset.deadline;
            bukaModal();
        });
    });

    // Tutup modal: tombol batal, klik area gelap, atau tekan Esc
    document.getElementById('btnBatal').addEventListener('click', tutupModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) tutupModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') tutupModal();
    });

    // Centang tugas -> langsung kirim form toggle
    document.querySelectorAll('.cek-tugas').forEach(function (cek) {
        cek.addEventListener('change', function () {
            cek.closest('form').submit();
        });
    });

    // Ganti urutan -> langsung kirim form
    document.querySelectorAll('.pilih-urut').forEach(function (pilih) {
        pilih.addEventListener('change', function () {
            pilih.closest('form').submit();
        });
    });

    // Konfirmasi sebelum hapus
    document.querySelectorAll('.form-hapus').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Yakin mau hapus tugas ini?')) {
                e.preventDefault();
            }
        });
    });
});