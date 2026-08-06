@extends('layouts.app')

@section('titulo', 'Professores · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Professores</h1>
        <p class="page-header__subtitle mb-0">{{ $professores->count() }} professor(es) cadastrado(s)</p>
    </div>
    <button type="button" class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#modalNovoProfessor">
        <i class="bi bi-plus-lg"></i> Novo Professor
    </button>
</div>

@if(session('sucesso'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form action="/grade/professores" method="GET" class="d-flex gap-2">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="{{ $busca }}">
            <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if($busca)
                <a href="/grade/professores" class="btn btn-outline-secondary">Limpar</a>
            @endif
        </form>
    </div>
</div>

@if($professores->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-person-badge"></i>
                <strong>Nenhum professor cadastrado</strong>
                Cadastre o primeiro professor para começar.
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Nome</th>
                            <th>Contato</th>
                            <th>Vínculos</th>
                            <th>Disponibilidade</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($professores as $professor)
                            <tr>
                                <td class="ps-4 fw-semibold text-brand">{{ $professor->nome }}</td>
                                <td><small>{{ $professor->email }} {{ $professor->telefone ? '· '.$professor->telefone : '' }}</small></td>
                                <td>
                                    <a href="/grade/professores/{{ $professor->id }}/vinculos">
                                        {{ $professor->vinculos_count }} vínculo(s)
                                    </a>
                                </td>
                                <td>
                                    <a href="/grade/professores/{{ $professor->id }}/disponibilidade">
                                        {{ $professor->disponibilidades_count }} slot(s)
                                    </a>
                                </td>
                                <td>
                                    @if($professor->ativo)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/grade/horarios/professor/{{ $professor->id }}" class="btn btn-sm btn-outline-secondary" title="Grade individual">
                                            <i class="bi bi-calendar3"></i>
                                        </a>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $professor->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="/grade/professores/{{ $professor->id }}" method="POST"
                                            data-confirm="Excluir este professor? Vínculos e disponibilidade também serão excluídos." data-confirm-tipo="danger">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modais de edição --}}
    @foreach($professores as $professor)
        <div class="modal fade" id="modalEditar{{ $professor->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-brand text-white">
                        <h5 class="modal-title">Editar Professor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/grade/professores/{{ $professor->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" value="{{ $professor->nome }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $professor->email }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" class="form-control" value="{{ $professor->telefone }}">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo{{ $professor->id }}" {{ $professor->ativo ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo{{ $professor->id }}">Ativo</label>
                            </div>
                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-brand fw-bold">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

{{-- Modal de criação --}}
<div class="modal fade" id="modalNovoProfessor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-brand text-white">
                <h5 class="modal-title">Novo Professor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/grade/professores" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}" value="{{ old('nome') }}" required>
                        @if($errors->has('nome'))
                            <div class="invalid-feedback">{{ $errors->first('nome') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="{{ old('telefone') }}">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativoNovo" checked>
                        <label class="form-check-label" for="ativoNovo">Ativo</label>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand fw-bold">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalNovoProfessor')).show();
            });
        </script>
    @endif
@endsection
