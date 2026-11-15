(function () {
    'use strict';

    document.querySelectorAll('.toast[data-auto]').forEach(function (el) {
        setTimeout(function () {
            el.classList.add('sembunyi');
            setTimeout(function () { el.remove(); }, 350);
        }, 3000);
    });
})();