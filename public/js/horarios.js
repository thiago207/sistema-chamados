$(document).ready(function () {

    $('.btn-celula').on('click', function () {
        const dia = $(this).data('dia');
        const turno = $(this).data('turno');
        const numero = $(this).data('numero');
        const titulo = $(this).data('titulo');
        const chave = `${dia}_${turno}_${numero}`;
        const opcoes = window.opcoesPorSlot[chave] || [];

        $('#campoDia').val(dia);
        $('#campoTurno').val(turno);
        $('#campoNumero').val(numero);
        $('#modalCelulaTitulo').text(titulo);

        const select = $('#selectOpcoesCelula');
        select.html('<option value="">— remover aula deste horário —</option>');

        opcoes.forEach(function (opcao) {
            select.append(`<option value="${opcao.materia_id}">${opcao.materia_nome} — ${opcao.professor_nome}</option>`);
        });

        new bootstrap.Modal(document.getElementById('modalCelula')).show();
    });

});
