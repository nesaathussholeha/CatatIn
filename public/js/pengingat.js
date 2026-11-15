document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal-pengingat');
    const tombolTambah = document.getElementById('buka-modal');
    const tombolTutup = document.getElementById('tutup-modal');
    const tombolBatal = document.getElementById('batal-modal');

    const form = document.getElementById('form-pengingat');
    const judulModal = document.getElementById('judul-modal');

    const inputAksi = document.getElementById('aksi');
    const inputId = document.getElementById('id-tugas');
    const inputJudul = document.getElementById('judul-tugas');
    const inputDeadline = document.getElementById('deadline-tugas');

    function bukaModal() {
        modal.classList.add('aktif');
    }

    function tutupModal() {
        modal.classList.remove('aktif');
    }

    function resetForm() {
        form.reset();
        inputAksi.value = 'tambah';
        inputId.value = '';
        judulModal.textContent = 'Tambah Tugas';
    }

    tombolTambah.addEventListener('click', function () {
        resetForm();
        bukaModal();
    });

    tombolTutup.addEventListener('click', tutupModal);
    tombolBatal.addEventListener('click', tutupModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            tutupModal();
        }
    });

    document.querySelectorAll('.tombol-edit').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            resetForm();

            inputAksi.value = 'edit';
            inputId.value = this.dataset.id;
            inputJudul.value = this.dataset.judul;
            inputDeadline.value = this.dataset.deadline;

            judulModal.textContent = 'Edit Tugas';

            bukaModal();
        });
    });
});