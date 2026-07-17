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
                        <option value="pendente"     {{ request('status') == 'pendente'     ? 'selected' : '' }}>Pendente</option>
                        <option value="em_andamento" {{ request('status') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                        <option value="pausada"      {{ request('status') == 'pausada'      ? 'selected' : '' }}>Pausada</option>
                        <option value="concluida"    {{ request('status') == 'concluida'    ? 'selected' : '' }}>Concluída</option>
                        <option value="cancelada"    {{ request('status') == 'cancelada'    ? 'selected' : '' }}>Cancelada</option>
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

{{-- TABELA --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Título</th>
                        <th>Criado por</th>
                        <th>Responsáveis</th>
                        <th>Status</th>
                        <th>Prazo</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarefas as $tarefa)
                        <tr>
                            <td class="ps-4 text-muted">{{ $tarefa->id }}</td>

                            <td class="fw-semibold text-brand">{{ $tarefa->titulo }}</td>

                            <td>
                                <small>{{ $tarefa->criador->name ?? 'Removido' }}</small>
                            </td>

                            <td>
                                @foreach($tarefa->responsaveis as $responsavel)
                                    <span class="badge rounded-pill">
                                        {{ $responsavel->name }}
                                    </span>
                                @endforeach
                            </td>

                            <td>
                                @include('tarefas.partials.status-badge', ['status' => $tarefa->status])
                            </td>

                            <td>
                                @if($tarefa->prazo)
                                    @php
                                        $atrasada = $tarefa->prazo->isPast()
                                                    && !in_array($tarefa->status, ['concluida', 'cancelada']);
                                    @endphp
                                    <small class="{{ $atrasada ? 'text-danger fw-bold' : '' }}">
                                        {{ $tarefa->prazo->format('d/m/Y') }}
                                        @if($atrasada)
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-flex gap-1 justify-content-end">

                                    {{-- Olhinho: detalhes --}}
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetalhes{{ $tarefa->id }}"
                                            title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Ações contextuais --}}
                                    @if(in_array($tarefa->status, ['pendente', 'pausada']))
                                        <form action="/tarefas/{{ $tarefa->id }}/iniciar" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-primary" title="Iniciar">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($tarefa->status === 'em_andamento')
                                        <button class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalConcluir{{ $tarefa->id }}"
                                                title="Concluir">
                                            <i class="bi bi-check-lg"></i>
                                        </button>

                                        <form action="/tarefas/{{ $tarefa->id }}/pausar" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Pausar">
                                                <i class="bi bi-pause-fill"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if(!in_array($tarefa->status, ['concluida', 'cancelada']))
                                        <form action="/tarefas/{{ $tarefa->id }}/cancelar" method="POST"
                                              onsubmit="return confirm('Cancelar esta tarefa?')">
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
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <strong>Nenhuma tarefa encontrada</strong>
                                    Ajuste os filtros ou registre uma nova tarefa.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- ===== MODAIS — fora da tabela ===== --}}
@foreach($tarefas as $tarefa)

    {{-- Modal de detalhes --}}
    <div class="modal fade" id="modalDetalhes{{ $tarefa->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-brand text-white">
                    <h5 class="modal-title">Tarefa #{{ $tarefa->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <h5 class="fw-bold mb-3 text-brand">{{ $tarefa->titulo }}</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Status</small>
                            @include('tarefas.partials.status-badge', ['status' => $tarefa->status])
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Prazo</small>
                            <span class="fw-bold">
                                {{ $tarefa->prazo ? $tarefa->prazo->format('d/m/Y') : 'Sem prazo' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Criado por</small>
                            <span class="fw-bold">{{ $tarefa->criador->name ?? 'Removido' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Criado em</small>
                            <span class="fw-bold">{{ $tarefa->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Responsáveis</small>
                        @foreach($tarefa->responsaveis as $responsavel)
                            <span class="badge rounded-pill">{{ $responsavel->name }}</span>
                        @endforeach
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">Descrição</small>
                        <p class="mb-0">{!! nl2br(e($tarefa->descricao)) !!}</p>
                    </div>

                    @if($tarefa->observacoes)
                        <div class="mb-3">
                            <small class="text-muted d-block">Observações</small>
                            <p class="mb-0">{!! nl2br(e($tarefa->observacoes)) !!}</p>
                        </div>
                    @endif

                    @if($tarefa->resolucao)
                        <hr>
                        <div class="alert alert-success mb-0">
                            <small class="text-muted d-block">
                                Resolução
                                @if($tarefa->concluida_em)
                                    — concluída em {{ $tarefa->concluida_em->format('d/m/Y H:i') }}
                                @endif
                            </small>
                            <p class="mb-0 mt-1">{!! nl2br(e($tarefa->resolucao)) !!}</p>
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de conclusão --}}
    @if($tarefa->status === 'em_andamento')
        <div class="modal fade" id="modalConcluir{{ $tarefa->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-white bg-success">
                        <h5 class="modal-title">Concluir Tarefa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="/tarefas/{{ $tarefa->id }}/concluir" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p class="text-muted small mb-3">{{ $tarefa->titulo }}</p>

                            <label class="form-label">
                                O que foi feito? <span class="text-danger">*</span>
                            </label>
                            <textarea name="resolucao" rows="5" class="form-control"
                                      placeholder="Descreva como a tarefa foi resolvida..." required></textarea>
                            <small class="text-muted">Mínimo de 10 caracteres.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success fw-bold">Concluir Tarefa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endforeach

@endsection
