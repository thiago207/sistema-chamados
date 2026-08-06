@extends('layouts.app')

@section('titulo', 'Grade de Horários · Sale Marketing')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Grade de Horários</h1>
        <p class="page-header__subtitle mb-0">
            @if($escola)
                {{ $escola->nome }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/grade/exportar/pdf/geral" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-pdf"></i> PDF geral</a>
        <a href="/grade/exportar/excel" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-excel"></i> Excel</a>
    </div>
</div>

<div class="row g-4">
    {{-- Coluna esquerda: indicadores + navegação --}}
    <div class="col-lg-4">
        @if($totalInconsistencias > 0)
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <strong>{{ $totalInconsistencias }} inconsistência(s) encontrada(s)</strong>
                    <div class="small fw-normal">Clique numa série/turma ou professor pra ver o detalhe</div>
                </div>
                <div class="card-body">
                    @include('grade.partials.inconsistencias', [
                        'porTurma' => $inconsistenciasPorTurma,
                        'porProfessor' => $inconsistenciasPorProfessor,
                        'idPrefix' => 'inicio',
                    ])
                </div>
            </div>
        @elseif($resumo)
            @php
                $percentual = $resumo['totalAulasEsperadas'] > 0
                    ? round(($resumo['totalAulasGeradas'] / $resumo['totalAulasEsperadas']) * 100)
                    : null;
            @endphp
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle"></i>
                <span>
                    Nenhuma inconsistência encontrada.
                    @if($percentual !== null)
                        A grade atual cobre {{ $percentual }}% da matriz curricular.
                    @endif
                </span>
            </div>
            <div class="row g-2 mb-3">
                @foreach([
                    ['icone' => 'bi-easel', 'valor' => $resumo['totalTurmas'], 'rotulo' => 'Séries/turmas'],
                    ['icone' => 'bi-person-badge', 'valor' => $resumo['totalProfessores'], 'rotulo' => 'Professores ativos'],
                    ['icone' => 'bi-calendar3', 'valor' => $resumo['totalAulasGeradas'].'/'.$resumo['totalAulasEsperadas'], 'rotulo' => 'Aulas alocadas'],
                ] as $stat)
                    <div class="col-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-3">
                                <i class="bi {{ $stat['icone'] }} fs-4 text-brand mb-1 d-block"></i>
                                <div class="fw-bold">{{ $stat['valor'] }}</div>
                                <div class="text-muted" style="font-size: .75rem">{{ $stat['rotulo'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="card">
            <div class="list-group list-group-flush">
                @foreach([
                    ['icone' => 'bi-journal-bookmark', 'titulo' => 'Matérias', 'url' => '/grade/materias'],
                    ['icone' => 'bi-easel', 'titulo' => 'Séries/Turmas', 'url' => '/grade/turmas'],
                    ['icone' => 'bi-person-badge', 'titulo' => 'Professores', 'url' => '/grade/professores'],
                    ['icone' => 'bi-magic', 'titulo' => 'Gerar Grade', 'url' => '/grade/gerar'],
                    ['icone' => 'bi-calendar3', 'titulo' => 'Editar Horários', 'url' => '/grade/horarios'],
                ] as $link)
                    <a href="{{ $link['url'] }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="bi {{ $link['icone'] }} text-brand"></i>
                        <span>{{ $link['titulo'] }}</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Coluna direita: todas as séries/turmas, clique expande o horário --}}
    <div class="col-lg-8">
        @if($turmas->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <strong>Nenhuma série/turma cadastrada</strong>
                        <a href="/grade/turmas/criar">Cadastrar a primeira</a>
                    </div>
                </div>
            </div>
        @else
            @php
                $diasNomes = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
            @endphp

            <div class="accordion" id="accordionTurmas">
                @foreach($turmas as $turma)
                    @php
                        $problemas = $inconsistenciasPorTurma->get($turma->id, collect());
                        $grade = $grades->get($turma->id, []);
                        $temHorario = ! empty($grade);
                        $dias = collect($turma->dias_semana)->sort()->values();
                    @endphp
                    <div class="accordion-item mb-2 border {{ $problemas->isNotEmpty() ? 'border-warning' : '' }}">
                        <h2 class="accordion-header d-flex align-items-stretch">
                            <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#turma{{ $turma->id }}">
                                <span class="fw-semibold">{{ $turma->nome }}</span>
                                @if($problemas->isNotEmpty())
                                    <span class="badge bg-warning text-dark ms-2">{{ $problemas->count() }} inconsistência(s)</span>
                                @elseif(! $temHorario)
                                    <span class="badge bg-secondary ms-2">Sem horário gerado</span>
                                @endif
                            </button>
                            <a href="/grade/horarios?turma_id={{ $turma->id }}" class="btn btn-sm btn-outline-secondary align-self-center me-3 ms-2 text-nowrap">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </h2>
                        <div id="turma{{ $turma->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionTurmas">
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

                                @if(! $temHorario)
                                    <p class="text-muted mb-0">Não tem horário cadastrado para essa série/turma ainda.</p>
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
    </div>
</div>

@endsection
