// Melhorias de interface apenas — não interfere na lógica de negócio/jQuery existente.
$(function () {

    // Tooltips do Bootstrap para qualquer elemento com atributo title
    document.querySelectorAll('[title]').forEach(function (el) {
        if (window.bootstrap && bootstrap.Tooltip) {
            new bootstrap.Tooltip(el);
        }
    });

    // Some automaticamente os alertas de sucesso após alguns segundos
    setTimeout(function () {
        $('.alert-success').each(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(this).close();
            }
        });
    }, 5000);

    // Estado de "carregando" no botão de envio — apenas formulários sem
    // confirmação nativa (para não travar visualmente se o usuário cancelar).
    $('form:not([onsubmit])').on('submit', function () {
        $(this).find('button[type="submit"]').addClass('is-loading').prop('disabled', true);
    });

});
