$(document).ready(function () {

    $('.select-responsaveis').select2({
        theme: 'bootstrap-5',
        placeholder: $(this).data('placeholder'),
        width: '100%',
        language: {
            noResults: function () {
                return 'Nenhum usuário encontrado';
            }
        }
    });

});