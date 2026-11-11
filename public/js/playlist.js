document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('plModal');
    var open  = document.getElementById('plOpenModal');
    var close = document.getElementById('plCloseModal');
    if (!modal || !open) return;

    open.addEventListener('click', function () {
        modal.hidden = false;
        document.getElementById('plNama').focus();
    });
    close.addEventListener('click', function () { modal.hidden = true; });
    modal.addEventListener('click', function (e) {
        if (e.target === modal) modal.hidden = true;
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') modal.hidden = true;
    });
});