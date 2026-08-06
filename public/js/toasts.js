$(document).ready(function () {
    document.querySelectorAll('#toastContainer .toast').forEach(function (elemento) {
        new bootstrap.Toast(elemento).show();
    });
});
