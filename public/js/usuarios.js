$(document).ready(function () {

    // Mostra/esconde o select de escola conforme o papel escolhido
    function alternarGrupoEscola(select, animado) {
        const grupoEscola = $(select).closest('form').find('.grupo-escola');
        const mostrar = $(select).val() === 'grade';

        if (!animado) {
            grupoEscola.toggle(mostrar);
            return;
        }

        if (mostrar) {
            grupoEscola.slideDown();
        } else {
            grupoEscola.slideUp();
            grupoEscola.find('select').val('');
        }
    }

    $('.campo-papel').each(function () {
        alternarGrupoEscola(this, false);
    });

    $(document).on('change', '.campo-papel', function () {
        alternarGrupoEscola(this, true);
    });

    // Mostra/esconde o campo "confirmar senha" conforme o usuário digita
    $('.campo-senha').on('input', function () {
        // Sobe até o modal-body e busca o grupo confirmar dentro dele
        const grupoConfirmar = $(this).closest('.modal-body').find('.grupo-confirmar');

        if ($(this).val().length > 0) {
            grupoConfirmar.slideDown();
        } else {
            grupoConfirmar.slideUp();
            grupoConfirmar.find('input').val('');
        }
    });

    // Botão do olhinho
    $('.btn-olho').on('click', function () {
        const campoSenha = $(this).closest('.input-group').find('.campo-senha');
        const icone = $(this).find('i');

        if (campoSenha.attr('type') === 'password') {
            campoSenha.attr('type', 'text');
            icone.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            campoSenha.attr('type', 'password');
            icone.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

});