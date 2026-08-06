$(document).ready(function () {

    // Ao escolher a turma, carrega por AJAX só as matérias que essa turma
    // tem na carga horária
    $('#selectTurma').on('change', function () {
        const turmaId = $(this).val();
        const selectMateria = $('#selectMateria');

        selectMateria.prop('disabled', true).html('<option value="" selected disabled>Carregando...</option>');

        $.getJSON(`/grade/turmas/${turmaId}/materias-json`, function (materias) {
            if (materias.length === 0) {
                selectMateria.html('<option value="" selected disabled>Essa turma não tem carga horária definida</option>');
                return;
            }

            let opcoes = '<option value="" selected disabled>Selecione a matéria...</option>';
            materias.forEach(function (materia) {
                opcoes += `<option value="${materia.id}">${materia.nome}</option>`;
            });

            selectMateria.html(opcoes).prop('disabled', false);
        });
    });

});
