$(document).ready(function () {

    // Mostra/esconde os campos de aulas/horário conforme o turno é marcado
    $(document).on('change', '.campo-turno', function () {
        const alvo = $(this).data('alvo');
        $(alvo).toggle(this.checked);
        calcularTermino();
        calcularTotalMaximo();
    });

    // Calcula e mostra o horário de término previsto de cada turno
    function calcularTermino() {
        const duracao = parseInt($('#campoDuracao').val(), 10) || 0;

        [['manha', '.campo-inicio-manha', 'input[name=aulas_manha]', '.termino-manha'],
         ['tarde', '.campo-inicio-tarde', 'input[name=aulas_tarde]', '.termino-tarde']]
            .forEach(function ([turno, seletorInicio, seletorAulas, seletorTermino]) {
                const inicio = $(seletorInicio).val();
                const aulas = parseInt($(seletorAulas).val(), 10) || 0;

                if (!inicio || !aulas || !duracao) {
                    $(seletorTermino).text('—');
                    return;
                }

                const [h, m] = inicio.split(':').map(Number);
                const minutosFim = h * 60 + m + (aulas * duracao);
                const hFim = Math.floor(minutosFim / 60) % 24;
                const mFim = minutosFim % 60;

                $(seletorTermino).text(
                    String(hFim).padStart(2, '0') + ':' + String(mFim).padStart(2, '0')
                );
            });
    }

    $(document).on('input change', '#campoDuracao, .campo-inicio-manha, .campo-inicio-tarde, input[name=aulas_manha], input[name=aulas_tarde]', function () {
        calcularTermino();
        calcularTotalMaximo();
    });

    // Quantos slots semanais a configuração atual do formulário permite
    function totalSlotsAtual() {
        const dias = $('input[name="dias_semana[]"]:checked').length;
        const aulasManha = $('#temManha').is(':checked') ? (parseInt($('input[name=aulas_manha]').val(), 10) || 0) : 0;
        const aulasTarde = $('#temTarde').is(':checked') ? (parseInt($('input[name=aulas_tarde]').val(), 10) || 0) : 0;

        return dias * (aulasManha + aulasTarde);
    }

    function calcularTotalMaximo() {
        $('#totalMaximo').text(totalSlotsAtual());
        atualizarTotalAlocado();
    }

    $(document).on('change', 'input[name="dias_semana[]"]', calcularTotalMaximo);

    // ---- Matriz curricular (disciplinas + quantidade de aulas) ----

    $('#selectMaterias').select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione as disciplinas...',
    });

    function nomeDaMateria(id) {
        const materia = (window.materiasDisponiveis || []).find(m => m.id == id);
        return materia ? materia.nome : '';
    }

    function renderizarLinhasMaterias() {
        const selecionadas = $('#selectMaterias').val() || [];
        const container = $('#linhasQuantidade');
        const valoresAtuais = {};

        container.find('input').each(function () {
            valoresAtuais[$(this).data('materia-id')] = $(this).val();
        });

        container.empty();

        selecionadas.forEach(function (id) {
            const valorAtual = valoresAtuais[id] ?? (window.quantidadesAtuais || {})[id] ?? '';

            container.append(`
                <div class="row align-items-center mb-2">
                    <div class="col-6">${nomeDaMateria(id)}</div>
                    <div class="col-6">
                        <input type="number" class="form-control campo-quantidade"
                            name="quantidades[${id}]" data-materia-id="${id}"
                            min="1" max="20" value="${valorAtual}" placeholder="Aulas por semana">
                    </div>
                </div>
            `);
        });

        atualizarTotalAlocado();
    }

    function atualizarTotalAlocado() {
        let total = 0;
        $('.campo-quantidade').each(function () {
            total += parseInt($(this).val(), 10) || 0;
        });

        $('#totalAlocado').text(total);

        const maximo = totalSlotsAtual();
        const alerta = $('#alertaTotal');
        alerta.removeClass('alert-success alert-danger alert-secondary');

        if (total === 0) {
            alerta.addClass('alert-secondary');
        } else if (total > maximo) {
            alerta.addClass('alert-danger');
        } else {
            alerta.addClass('alert-success');
        }
    }

    $('#selectMaterias').on('change', renderizarLinhasMaterias);
    $(document).on('input', '.campo-quantidade', atualizarTotalAlocado);

    calcularTermino();
    calcularTotalMaximo();
    renderizarLinhasMaterias();

});
