document.addEventListener("DOMContentLoaded", function () {

    const token = document.querySelector('meta[name="token"]').content;
    const daftar = document.getElementById("daftarLaporan");
    const kosong = daftar.querySelector(".kosong");
    const chips = document.querySelectorAll(".chip");
    const dialog = document.getElementById("dialogDetail");

    let filterAktif = "Semua";

    /* ===== FILTER STATUS ===== */

    function terapkanFilter() {
        let tampil = 0;

        daftar.querySelectorAll("tr[data-id]").forEach(function (baris) {
            const cocok = filterAktif === "Semua" || baris.dataset.status === filterAktif;
            baris.hidden = !cocok;
            if (cocok) tampil++;
        });

        kosong.hidden = tampil > 0;
    }

    chips.forEach(function (chip) {
        chip.addEventListener("click", function () {
            chips.forEach(function (c) { c.classList.remove("aktif"); });
            chip.classList.add("aktif");
            filterAktif = chip.dataset.filter;
            terapkanFilter();
        });
    });

    /* ===== KIRIM PERUBAHAN KE PHP ===== */

    function kirim(data) {
        const body = new URLSearchParams(Object.assign({ token: token }, data));

        return fetch("kelola-laporan.php", { method: "POST", body: body })
            .then(function (res) { return res.json(); })
            .then(function (hasil) { return hasil.ok; })
            .catch(function () { return false; });
    }

    /* ===== UBAH STATUS ===== */

    daftar.addEventListener("change", function (e) {
        if (!e.target.classList.contains("pilih-status")) return;

        const baris = e.target.closest("tr");
        const bungkus = e.target.closest(".status");
        const lama = baris.dataset.status;
        const baru = e.target.value;

        kirim({ aksi: "status", id: baris.dataset.id, status: baru }).then(function (ok) {
            if (!ok) {
                e.target.value = lama;
                alert("Status gagal diubah. Coba lagi.");
                return;
            }

            baris.dataset.status = baru;
            bungkus.className = "status " + baru.toLowerCase();
            terapkanFilter();
        });
    });

    /* ===== DETAIL & HAPUS ===== */

    daftar.addEventListener("click", function (e) {
        const link = e.target.closest("a");
        if (!link) return;

        e.preventDefault();
        const baris = link.closest("tr");

        if (link.classList.contains("detail")) {
            const sel = baris.querySelectorAll("td");

            document.getElementById("dCatatan").textContent = sel[0].textContent;
            document.getElementById("dPelapor").textContent = sel[1].textContent;
            document.getElementById("dAlasan").textContent = sel[2].textContent;
            document.getElementById("dStatus").textContent = baris.dataset.status;
            document.getElementById("dKeterangan").textContent = baris.dataset.keterangan || "-";

            dialog.showModal();
        }

        if (link.classList.contains("hapus")) {
            if (!confirm("Hapus laporan ini?")) return;

            kirim({ aksi: "hapus", id: baris.dataset.id }).then(function (ok) {
                if (!ok) {
                    alert("Laporan gagal dihapus.");
                    return;
                }

                baris.remove();
                terapkanFilter();
            });
        }
    });

    /* Tutup dialog saat klik di luar kotak */
    dialog.addEventListener("click", function (e) {
        if (e.target === dialog) dialog.close();
    });

    terapkanFilter();
});