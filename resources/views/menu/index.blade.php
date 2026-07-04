@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="mb-4" style="color: #1a3a6b;">Bem-vindo, {{ session('usuario_nome') }}</h4>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <div class="card-body">
                <h5 class="fw-bold">Nova Tarefa</h5>
                <p class="text-muted">Criar um novo chamado</p>
                <a href="/tarefas/criar" class="btn text-white" style="background-color: #c0392b;">Acessar</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <div class="card-body">
                <h5 class="fw-bold">Tarefas</h5>
                <p class="text-muted">Ver tarefas em andamento</p>
                <a href="/tarefas" class="btn text-white" style="background-color: #c0392b;">Acessar</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <div class="card-body">
                <h5 class="fw-bold">Histórico</h5>
                <p class="text-muted">Ver tarefas concluídas</p>
                <a href="/historico" class="btn text-white" style="background-color: #c0392b;">Acessar</a>
            </div>
        </div>
    </div>
</div>
@endsection