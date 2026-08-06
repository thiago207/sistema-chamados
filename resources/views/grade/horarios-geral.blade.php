@extends('layouts.app')

@section('titulo', 'Visão Geral · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Horários', 'url' => '/grade/horarios'],
    ['label' => 'Visão Geral'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Visão Geral</h1>
        <p class="page-header__subtitle mb-0">Todas as séries/turmas de uma vez — {{ $turmas->count() }} no total</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/grade/gerar" class="btn btn-outline-secondary"><i class="bi bi-magic"></i> Gerar Grade</a>
        <a href="/grade/exportar/pdf/geral" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-pdf"></i> PDF geral</a>
        <a href="/grade/exportar/excel" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-excel"></i> Excel</a>
    </div>
</div>

@if($problemasPorTurma->isNotEmpty())
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle"></i>
        <span>{{ $problemasPorTurma->flatten(1)->count() }} inconsistência(s) encontrada(s) em {{ $problemasPorTurma->count() }} série(s)/turma(s) — marcadas abaixo. Veja também <a href="/grade/gerar">o diagnóstico completo</a>.</span>
    </div>
@endif

@if($turmas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <strong>Nenhuma série/turma cadastrada</strong>
            </div>
        </div>
    </div>
@else
    @php
        $diasNomes = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
    @endphp

    <div class="accordion" id="accordionGeral">
        @foreach($turmas as $turma)
            @php
                $problemas = $problemasPorTurma->get($turma->id, collect());
                $grade = $grades->get($turma->id, []);
                $dias = collect($turma->dias_semana)->sort()->values();
            @endphp
            <div class="accordion-item mb-2 border {{ $problemas->isNotEmpty() ? 'border-warning' : '' }}">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#turma{{ $turma->id }}">
                        <span class="fw-semibold">{{ $turma->nome }}</span>
                        @if($problemas->isNotEmpty())
                            <span class="badge bg-warning text-dark ms-2">{{ $problemas->count() }} inconsistência(s)</span>
                        @endif
                        <a href="/grade/horarios?turma_id={{ $turma->id }}" class="btn btn-sm btn-outline-secondary ms-auto me-2" onclick="event.stopPropagation()">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    </button>
                </h2>
                <div id="turma{{ $turma->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionGeral">
                    <div class="accordion-body">
                        @if($problemas->isNotEmpty())
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($problemas as $problema)
                                    <li class="list-group-item d-flex gap-2">
                                        <i class="bi bi-exclamation-triangle text-warning"></i>
                                        <span>{{ $problema['mensagem'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(empty($grade))
                            <p class="text-muted mb-0">Nenhum horário gerado ainda para essa série/turma.</p>
                        @else
                            @foreach(['manha' => ['Manhã', $turma->aulas_manha], 'tarde' => ['Tarde', $turma->aulas_tarde]] as $turno => [$rotulo, $max])
                                @if($max > 0)
                                    <div class="fw-semibold mb-1">{{ $rotulo }}</div>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered table-sm text-center mb-0 align-middle">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    @foreach($dias as $dia)
                                                        <th>{{ $diasNomes[$dia] }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($numero = 1; $numero <= $max; $numero++)
                                                    <tr>
                                                        <td class="text-muted"><small>{{ $numero }}ª</small></td>
                                                        @foreach($dias as $dia)
                                                            @php $horario = $grade[$turno][$numero][$dia] ?? null; @endphp
                                                            <td>
                                                                @if($horario)
                                                                    <small>{{ $horario->materia->nome }}<br>{{ $horario->professor->nome }}</small>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
