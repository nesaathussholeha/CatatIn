/* upload.js — khusus halaman unggah catatan.
   Menu sidebar mobile sudah ditangani layout_user/script.js. */
(function () {
    'use strict';

    // Anonim hanya relevan untuk catatan publik
    var radios = document.querySelectorAll('input[name="visibilitas"]');
    var anonim = document.getElementById('is_anonim');
    var grup   = document.getElementById('grup-anonim');
    if (!radios.length || !anonim || !grup) { return; }

    function sinkron() {
        var publik = document.querySelector('input[name="visibilitas"]:checked').value === 'publik';
        anonim.disabled = !publik;
        if (!publik) { anonim.checked = false; }
        grup.style.opacity = publik ? '' : '.5';
    }

    radios.forEach(function (r) { r.addEventListener('change', sinkron); });
    sinkron();
})();