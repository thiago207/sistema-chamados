@extends('layouts.app')

@section('titulo', 'Professores · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores'],
]])

@include('grade.partials.subnav')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Professores</h1>
        <p class="page-header__subtitle mb-0">{{ $professores->count() }} professor(es) cadastrado(s)</p>
    </div>
    <a href="/grade/professores/criar" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Novo Professor
    </a>
</div>

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
                                <td class="ps-4 fw-semibold text-brand">
                                    <a href="/grade/professores/{{ $professor->id }}/editar" class="text-decoration-none">{{ $professor->nome }}</a>
                                </td>
                                <td><small>{{ $professor->email }} {{ $professor->telefone ? '· '.$professor->telefone : '' }}</small></td>
                                <td>
                                    <a href="/grade/professores/{{ $professor->id }}/vinculos" class="{{ $professor->vinculos_count === 0 ? 'text-danger' : '' }}">
                                        {{ $professor->vinculos_count }} vínculo(s)
                                    </a>
                                </td>
                                <td>
                                    <a href="/grade/professores/{{ $professor->id }}/disponibilidade" class="{{ $professor->disponibilidades_count === 0 ? 'text-danger' : '' }}">
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
                                        <a href="/grade/professores/{{ $professor->id }}/disponibilidade" class="btn btn-sm btn-outline-secondary" title="Disponibilidade">
                                            <i class="bi bi-calendar-check"></i>
                                        </a>
                                        <a href="/grade/professores/{{ $professor->id }}/editar" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/grade/professores/{{ $professor->id }}" method="POST"
                                            data-confirm="Excluir este professor? Vínculos e disponibilidade também serão excluídos." data-confirm-tipo="danger">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
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
@endif

@endsection
