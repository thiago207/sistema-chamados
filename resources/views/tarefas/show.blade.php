@extends('layouts.app')

@section('titulo', $tarefa->titulo . ' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Tarefas', 'url' => '/tarefas'],
    ['label' => $tarefa->titulo],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">{{ $tarefa->titulo }}</h1>
        <p class="page-header__subtitle mb-0">Tarefa #{{ $tarefa->id }}</p>
    </div>
    <a href="/tarefas" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="card">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Status</small>
                        @include('tarefas.partials.status-badge', ['status' => $tarefa->status])
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Data a ser feita</small>
                        @php
                            $atrasada = $tarefa->data->isPast() && $tarefa->status === 'pendente';
                        @endphp
                        <span class="fw-bold {{ $atrasada ? 'text-danger' : '' }}">
                            {{ $tarefa->data->format('d/m/Y') }}
                            @if($atrasada)
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            @endif
                        </span>
                    </div>
                    @if($tarefa->status === 'concluida' && $tarefa->concluida_em)
                        <div class="col-12 col-md-4 mt-3 mt-md-0">
                            <small class="text-muted d-block">Concluída em</small>
                            <span class="fw-bold">{{ $tarefa->concluida_em->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Criado por</small>
                        <span class="fw-bold">{{ $tarefa->criador->name ?? 'Removido' }}</span>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <small class="text-muted d-block">Criado em</small>
                        <span class="fw-bold">{{ $tarefa->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Responsáveis</small>
                    @forelse($tarefa->responsaveis as $responsavel)
                        <span class="badge rounded-pill">{{ $responsavel->name }}</span>
                    @empty
                        <span class="text-muted">Ninguém atribuído</span>
                    @endforelse
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block">Descrição</small>
                    <p class="mb-0">{!! nl2br(e($tarefa->descricao)) !!}</p>
                </div>

                @if($tarefa->observacoes)
                    <div class="mb-0">
                        <small class="text-muted d-block">Observações</small>
                        <p class="mb-0">{!! nl2br(e($tarefa->observacoes)) !!}</p>
                    </div>
                @endif

            </div>

            @if($tarefa->status === 'pendente')
                <div class="card-footer bg-white border-top">
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <form action="/tarefas/{{ $tarefa->id }}/concluir" method="POST"
                              data-confirm="Marcar esta tarefa como concluída?" class="flex-fill">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                                <i class="bi bi-check-lg"></i> Concluir tarefa
                            </button>
                        </form>

                        <form action="/tarefas/{{ $tarefa->id }}/cancelar" method="POST"
                              data-confirm="Cancelar esta tarefa?" data-confirm-tipo="danger" class="flex-fill">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-danger btn-lg w-100 fw-bold">
                                <i class="bi bi-x-lg"></i> Cancelar tarefa
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
