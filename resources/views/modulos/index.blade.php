@extends('layouts.app')

@section('titulo', 'Selecionar Módulo · Sale Marketing')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Selecionar Módulo</h1>
        <p class="page-header__subtitle mb-0">Escolha em qual área você quer trabalhar</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <a href="/tarefas" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-list-task display-4 text-brand mb-3 d-block"></i>
                    <h4 class="fw-bold mb-1">Tarefas</h4>
                    <p class="text-muted mb-0">Chamados, responsáveis e status</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6">
        <a href="/grade" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-calendar3 display-4 text-brand mb-3 d-block"></i>
                    <h4 class="fw-bold mb-1">Grade de Horários</h4>
                    <p class="text-muted mb-0">Cadastro escolar e geração de grade</p>
                </div>
            </div>
        </a>
    </div>
</div>

@endsection
