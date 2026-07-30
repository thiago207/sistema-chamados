$(function () {
    const badges = {
        pendente:  ['Pendente', 'bg-warning text-dark'],
        concluida: ['Concluída', 'bg-success'],
        cancelada: ['Cancelada', 'bg-danger'],
    };

    let modalDia; // instância única do modal (criada só uma vez)

    const loadingEl  = document.getElementById('calendarioLoading');
    const calendarEl = document.getElementById('calendario');

    // Abre o modal listando as tarefas do dia informado (YYYY-MM-DD).
    // Cada item é um link direto para a tarefa (/tarefas/{id}).
    function abrirTarefasDoDia(dateStr) {
        const eventos = calendar.getEvents().filter(ev => ev.startStr === dateStr);
        if (!eventos.length) {
            return;
        }

        const [ano, mes, dia] = dateStr.split('-');
        $('#modalDiaTitulo').text(`Tarefas de ${dia}/${mes}/${ano}`);

        const $lista = $('#modalDiaLista').empty();
        eventos.forEach(function (ev) {
            const props = ev.extendedProps;
            const [rotulo, classe] = badges[props.status] || ['—', 'bg-secondary'];

            $('<a>')
                .attr('href', `/tarefas/${ev.id}`)
                .addClass(`list-group-item list-group-item-action ${props.status}`)
                .append($('<span>').addClass('titulo').text(ev.title))
                .append($('<span>').addClass(`badge ${classe}`).text(rotulo))
                .appendTo($lista);
        });

        modalDia = modalDia || new bootstrap.Modal(document.getElementById('modalDia'));
        modalDia.show();
    }

    // No celular a célula do dia é estreita; mostrar poucos eventos
    // por dia (com link "+N mais") mantém o mês compacto e legível.
    const ehTelaPequena = window.matchMedia('(max-width: 575.98px)').matches;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        height: 'auto',
        dayMaxEvents: ehTelaPequena ? 2 : true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth',
        },

        // Traduções dos botões — a tradução da biblioteca não cobre
        // "Hoje", "Mês" e "Lista" no cabeçalho, então fixamos aqui.
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            list: 'Lista',
        },

        // Carimba cada evento com uma classe conforme o status.
        // O CSS lá em cima usa essa classe pra pintar o evento.
        eventClassNames: function (arg) {
            const s = arg.event.extendedProps.status;
            return s ? ['fc-status-' + s] : [];
        },

        // Dispara antes/depois de buscar os eventos no servidor.
        // Aqui só ligo/desligo o spinner.
        loading: function (isLoading) {
            loadingEl.classList.toggle('is-active', isLoading);
        },

        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: '/tarefas/eventos',
                data: { start: info.startStr, end: info.endStr },
                dataType: 'json',
            })
                .done(successCallback)
                .fail(failureCallback);
        },

        // Clicar num dia vazio ou num evento sempre abre a lista de
        // tarefas daquele dia — dali o usuário escolhe a tarefa específica.
        dateClick: function (info) {
            abrirTarefasDoDia(info.dateStr);
        },

        eventClick: function (info) {
            abrirTarefasDoDia(info.event.startStr);
        },
    });

    calendar.render();
});
