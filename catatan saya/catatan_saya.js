/* catatan_saya.js — khusus halaman catatan_saya.php
   (menu sidebar mobile ditangani oleh ../layout_user/script.js) */
(function () {
    'use strict';

    document.querySelectorAll('.toast[data-auto]').forEach(function (el) {
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, 3000);
    });
})();