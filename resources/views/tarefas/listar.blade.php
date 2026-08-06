@extends('layouts.app')

@section('titulo', 'Tarefas · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Tarefas'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Tarefas</h1>
        <p class="page-header__subtitle mb-0">{{ $tarefas->count() }} tarefa(s) no total</p>
    </div>
    <a href="/tarefas/criar" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Nova Tarefa
    </a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- FILTROS --}}
<div class="card filter-card mb-4">
    <div class="card-body">
        <form action="/tarefas" method="GET">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control"
                           value="{{ request('busca') }}"
                           placeholder="Título ou descrição...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente"  {{ request('status') == 'pendente'  ? 'selected' : '' }}>Pendente</option>
                        <option value="concluida" {{ request('status') == 'concluida' ? 'selected' : '' }}>Concluída</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Responsável</label>
                    <select name="responsavel" class="form-select">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                {{ request('responsavel') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" title="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="/tarefas" class="btn btn-outline-secondary flex-fill" title="Limpar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

@if($tarefas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <strong>Nenhuma tarefa encontrada</strong>
                Ajuste os filtros ou registre uma nova tarefa.
            </div>
        </div>
    </div>
@else

    {{-- CARTÕES — celular --}}
    <div class="d-md-none d-flex flex-column gap-3">
        @foreach($tarefas as $tarefa)
            @php
                $atrasada = $tarefa->data->isPast() && $tarefa->status === 'pendente';
            @endphp
            <div class="card task-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <a href="/tarefas/{{ $tarefa->id }}" class="fw-semibold text-brand task-card__titulo">
                            {{ $tarefa->titulo }}
                        </a>
                        @include('tarefas.partials.status-badge', ['status' => $tarefa->status])
                    </div>

                    <div class="task-card__meta text-muted small mb-2">
                        <span class="{{ $atrasada ? 'text-danger fw-bold' : '' }}">
                            <i class="bi bi-calendar3"></i> {{ $tarefa->data->format('d/m/Y') }}
                            @if($atrasada)
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            @endif
                        </span>
                    </div>

                    @if($tarefa->responsaveis->isNotEmpty())
                        <div class="mb-2">
                            @foreach($tarefa->responsaveis as $responsavel)
                                <span class="badge rounded-pill">{{ $responsavel->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <a href="/tarefas/{{ $tarefa->id }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-eye"></i> Ver
                        </a>

                        @if($tarefa->status === 'pendente')
                            <form action="/tarefas/{{ $tarefa->id }}/concluir" method="POST"
                                  data-confirm="Marcar esta tarefa como concluída?" class="flex-fill">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100" title="Concluir">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>

                            <form action="/tarefas/{{ $tarefa->id }}/cancelar" method="POST"
                                  data-confirm="Cancelar esta tarefa?" data-confirm-tipo="danger" class="flex-fill">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline-danger w-100" title="Cancelar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TABELA — telas médias e maiores --}}
    <div class="card d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Título</th>
                            <th>Responsáveis</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tarefas as $tarefa)
                            <tr>
                                <td class="ps-4 text-muted">{{ $tarefa->id }}</td>

                                <td>
                                    <a href="/tarefas/{{ $tarefa->id }}" class="fw-semibold text-brand">
                                        {{ $tarefa->titulo }}
                                    </a>
                                </td>

                                <td>
                                    @forelse($tarefa->responsaveis as $responsavel)
                                        <span class="badge rounded-pill">
                                            {{ $responsavel->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </td>

                                <td>
                                    @include('tarefas.partials.status-badge', ['status' => $tarefa->status])
                                </td>

                                <td>
                                    @php
                                        $atrasada = $tarefa->data->isPast() && $tarefa->status === 'pendente';
                                    @endphp
                                    <small class="{{ $atrasada ? 'text-danger fw-bold' : '' }}">
                                        {{ $tarefa->data->format('d/m/Y') }}
                                        @if($atrasada)
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        @endif
                                    </small>
                                </td>

                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end flex-wrap">

                                        <a href="/tarefas/{{ $tarefa->id }}" class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($tarefa->status === 'pendente')
                                            <form action="/tarefas/{{ $tarefa->id }}/concluir" method="POST"
                                                  data-confirm="Marcar esta tarefa como concluída?">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" title="Concluir">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>

                                            <form action="/tarefas/{{ $tarefa->id }}/cancelar" method="POST"
                                                  data-confirm="Cancelar esta tarefa?" data-confirm-tipo="danger">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@endsection
