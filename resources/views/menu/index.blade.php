@extends('layouts.app')

@section('titulo', 'Menu · Sale Marketing')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-header__title">Bem-vindo, {{ session('usuario_nome') }}</h1>
        <p class="page-header__subtitle mb-0">Atividades do mês</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/tarefas/criar" class="btn btn-brand">
            <i class="bi bi-plus-lg"></i> Nova Tarefa
        </a>
        <a href="/tarefas" class="btn btn-outline-secondary">
            <i class="bi bi-list-task"></i> Ver todas
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>

{{-- Modal de detalhes do evento --}}
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-brand text-white">
                <h5 class="modal-title" id="modalEventoTitulo">Tarefa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span id="modalEventoStatus"></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Prazo</small>
                    <span id="modalEventoPrazo" class="fw-bold"></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Responsáveis</small>
                    <span id="modalEventoResponsaveis"></span>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Descrição</small>
                    <p id="modalEventoDescricao" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="/tarefas" class="btn btn-outline-secondary">Ver na lista</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6/locales-all.global.min.js"></script>
    <script>
        $(function () {
            const badges = {
                pendente:     ['Pendente', 'bg-warning text-dark'],
                em_andamento: ['Em andamento', 'bg-primary'],
                pausada:      ['Pausada', 'bg-secondary'],
                concluida:    ['Concluída', 'bg-success'],
                cancelada:    ['Cancelada', 'bg-danger'],
            };

            const calendarEl = document.getElementById('calendario');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                initialView: 'dayGridMonth',
                height: 'auto',
                dayMaxEvents: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth',
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
                eventClick: function (info) {
                    const props = info.event.extendedProps;
                    const [rotulo, classe] = badges[props.status] || ['—', 'bg-secondary'];

                    $('#modalEventoTitulo').text(info.event.title);
                    $('#modalEventoStatus').html(`<span class="badge ${classe}">${rotulo}</span>`);
                    $('#modalEventoPrazo').text(info.event.start
                        ? info.event.start.toLocaleDateString('pt-BR')
                        : 'Sem prazo');
                    $('#modalEventoResponsaveis').text(props.responsaveis || 'Ninguém atribuído');
                    $('#modalEventoDescricao').text(props.descricao || '');

                    new bootstrap.Modal(document.getElementById('modalEvento')).show();
                },
            });

            calendar.render();
        });
    </script>
@endsection
@endsection
