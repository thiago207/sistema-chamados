@extends('layouts.app')

@section('titulo', 'Séries/Turmas · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Séries/Turmas'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Séries/Turmas</h1>
        <p class="page-header__subtitle mb-0">{{ $turmas->count() }} série(s)/turma(s) cadastrada(s)</p>
    </div>
    <a href="/grade/turmas/criar" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Nova Série/Turma
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="/grade/turmas" method="GET" class="d-flex gap-2">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="{{ $busca }}">
            <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if($busca)
                <a href="/grade/turmas" class="btn btn-outline-secondary">Limpar</a>
            @endif
        </form>
    </div>
</div>

@php
    $diasAbrev = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
    $resumoDias = fn ($dias) => collect($dias)->sort()->map(fn ($d) => $diasAbrev[$d] ?? '')->implode(', ');
    $resumoTurno = function ($turma, $turno) {
        $aulas = $turno === 'manha' ? $turma->aulas_manha : $turma->aulas_tarde;
        $inicio = $turno === 'manha' ? $turma->inicio_manha : $turma->inicio_tarde;
        if (! $aulas) {
            return null;
        }
        return ($turno === 'manha' ? 'Manhã' : 'Tarde').": {$aulas} aulas a partir das ".\Illuminate\Support\Carbon::parse($inicio)->format('H:i');
    };
@endphp

@if($turmas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-easel"></i>
                <strong>Nenhuma série/turma cadastrada</strong>
                Cadastre a primeira para começar.
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
                            <th>Dias</th>
                            <th>Turnos</th>
                            <th>Disciplinas</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turmas as $turma)
                            <tr>
                                <td class="ps-4 fw-semibold text-brand">
                                    <a href="/grade/turmas/{{ $turma->id }}" class="text-decoration-none">{{ $turma->nome }}</a>
                                </td>
                                <td><small>{{ $resumoDias($turma->dias_semana) }}</small></td>
                                <td>
                                    <small>
                                        {{ $resumoTurno($turma, 'manha') }}
                                        @if($resumoTurno($turma, 'manha') && $resumoTurno($turma, 'tarde'))<br>@endif
                                        {{ $resumoTurno($turma, 'tarde') }}
                                    </small>
                                </td>
                                <td>{{ $turma->materias_count }}</td>
                                <td class="pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/grade/turmas/{{ $turma->id }}" class="btn btn-sm btn-outline-secondary" title="Ver grade">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/grade/turmas/{{ $turma->id }}/editar" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/grade/turmas/{{ $turma->id }}/duplicar" method="POST"
                                            data-confirm="Duplicar &quot;{{ $turma->nome }}&quot;? Os dias, turnos e a matriz curricular serão copiados — só o nome precisa ser ajustado depois.">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicar">
                                                <i class="bi bi-copy"></i>
                                            </button>
                                        </form>
                                        <form action="/grade/turmas/{{ $turma->id }}" method="POST"
                                            data-confirm="Excluir esta série/turma? A matriz curricular, vínculos e horários gerados também serão excluídos." data-confirm-tipo="danger">
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
