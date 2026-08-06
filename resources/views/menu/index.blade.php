@extends('layouts.app')

@section('titulo', 'Menu · Sale Marketing')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Bem-vindooo, {{ session('usuario_nome') }}</h1>
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

{{-- Cards de resumo: só aparecem se o controller passar $resumo.
     Se não passar, o bloco é ignorado e nada quebra. --}}
@isset($resumo)
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100" style="border-left-color:#f59e0b">
            <div class="card-body">
                <div class="stat-card__num text-warning">{{ $resumo['pendentes'] ?? 0 }}</div>
                <div class="stat-card__label text-muted">Pendentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100" style="border-left-color:#22c55e">
            <div class="card-body">
                <div class="stat-card__num text-success">{{ $resumo['concluidas'] ?? 0 }}</div>
                <div class="stat-card__label text-muted">Concluídas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100" style="border-left-color:#6b7280">
            <div class="card-body">
                <div class="stat-card__num text-secondary">{{ $resumo['canceladas'] ?? 0 }}</div>
                <div class="stat-card__label text-muted">Canceladas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100" style="border-left-color:#ef4444">
            <div class="card-body">
                <div class="stat-card__num text-danger">{{ $resumo['atrasadas'] ?? 0 }}</div>
                <div class="stat-card__label text-muted">Atrasadas</div>
            </div>
        </div>
    </div>
</div>
@endisset

<div class="card">
    <div class="card-body">
        {{-- Legenda: explica o que cada cor significa --}}
        <div class="cal-legend mb-3 text-muted">
            <span><span class="dot" style="background:#f59e0b"></span>Pendente</span>
            <span><span class="dot" style="background:#22c55e"></span>Concluída</span>
            <span><span class="dot" style="background:#ef4444"></span>Cancelada</span>
        </div>

        <div id="calendarioWrap">
            <div id="calendarioLoading" aria-hidden="true">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando…</span>
                </div>
            </div>
            <div id="calendario"></div>
        </div>
    </div>
</div>

{{-- Modal com as tarefas do dia clicado --}}
<div class="modal fade" id="modalDia" tabindex="-1" aria-labelledby="modalDiaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-brand text-white">
                <h5 class="modal-title" id="modalDiaTitulo">Tarefas do dia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="list-group lista-tarefas-dia" id="modalDiaLista"></div>
            </div>
            <div class="modal-footer">
                <a href="/tarefas" class="btn btn-outline-secondary">Ver todas as tarefas</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6/locales-all.global.min.js"></script>
    <script src="{{ asset('js/menu.js') }}"></script>
@endsection
@endsection
