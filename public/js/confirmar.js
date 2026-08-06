// Substitui o confirm() nativo do navegador por um modal do SweetAlert2.
// Uso: em vez de onsubmit="return confirm('...')", coloque no <form>
// o atributo data-confirm="mensagem" (e opcionalmente data-confirm-tipo="danger").
$(document).on('submit', 'form[data-confirm]', function (evento) {
    const form = this;

    if (form.dataset.confirmado === '1') {
        return true;
    }

    evento.preventDefault();

    const mensagem = form.dataset.confirm;
    const perigoso = form.dataset.confirmTipo === 'danger';

    Swal.fire({
        text: mensagem,
        icon: perigoso ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: perigoso ? 'Sim, excluir' : 'Confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: perigoso ? '#c0392b' : '#1a3a6b',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
    }).then(function (resultado) {
        if (resultado.isConfirmed) {
            form.dataset.confirmado = '1';
            form.requestSubmit ? form.requestSubmit() : form.submit();
        }
    });
});
