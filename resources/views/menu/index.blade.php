@extends('layouts.app')

@section('titulo', 'Menu · Sale Marketing')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-header__title">Bem-vindo, {{ session('usuario_nome') }}</h1>
        <p class="page-header__subtitle mb-0">O que você gostaria de fazer hoje?</p>
    </div>
</div>

<div class="row g-4">

    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-card__icon">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h5 class="fw-bold mb-1">Nova Tarefa</h5>
                <p class="text-muted small mb-3">Criar um novo chamado</p>
                <a href="/tarefas/criar" class="btn btn-brand w-100">Acessar</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-card__icon">
                    <i class="bi bi-list-task"></i>
                </div>
                <h5 class="fw-bold mb-1">Tarefas</h5>
                <p class="text-muted small mb-3">Ver tarefas em andamento</p>
                <a href="/tarefas" class="btn btn-brand w-100">Acessar</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-card__icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h5 class="fw-bold mb-1">Histórico</h5>
                <p class="text-muted small mb-3">Ver tarefas concluídas</p>
                <a href="/historico" class="btn btn-brand w-100">Acessar</a>
            </div>
        </div>
    </div>

</div>
@endsection
