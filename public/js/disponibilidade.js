$(document).ready(function () {

    function atualizarTotal() {
        $('#totalMarcados').text($('.slot-checkbox:checked').length);
    }

    $('#btnMarcarTudo').on('click', function () {
        $('.slot-checkbox').prop('checked', true);
        atualizarTotal();
    });

    $('#btnLimparTudo').on('click', function () {
        $('.slot-checkbox').prop('checked', false);
        atualizarTotal();
    });

    $('.btn-marcar-turno').on('click', function () {
        const turno = $(this).data('turno');
        $(`.slot-checkbox[data-turno="${turno}"]`).prop('checked', true);
        atualizarTotal();
    });

    $('.btn-marcar-dia').on('click', function () {
        const dia = $(this).data('dia');
        $(`.slot-checkbox[data-dia="${dia}"]`).prop('checked', true);
        atualizarTotal();
    });

    $(document).on('change', '.slot-checkbox', atualizarTotal);

});
