@extends('layouts.app')

@section('titulo', 'Grade de '.$professor->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores', 'url' => '/grade/professores'],
    ['label' => $professor->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Grade de {{ $professor->nome }}</h1>
        <p class="page-header__subtitle mb-0">Horários individuais desse professor</p>
    </div>
    <a href="/grade/exportar/pdf/professor/{{ $professor->id }}" class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
    </a>
</div>

@php
    $diasNomes = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
@endphp

@if($maxManha === 0 && $maxTarde === 0)
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <strong>Sem horários gerados para este professor ainda</strong>
            </div>
        </div>
    </div>
@else
    @foreach(['manha' => ['Manhã', $maxManha], 'tarde' => ['Tarde', $maxTarde]] as $turno => [$rotulo, $max])
        @if($max > 0)
            <div class="card mb-3">
                <div class="card-header"><strong>{{ $rotulo }}</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th></th>
                                    @foreach($diasNomes as $dia => $nome)
                                        <th>{{ $nome }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for($numero = 1; $numero <= $max; $numero++)
                                    <tr>
                                        <td class="text-muted"><small>{{ $numero }}ª</small></td>
                                        @foreach($diasNomes as $dia => $nome)
                                            @php $horario = $grade[$turno][$numero][$dia] ?? null; @endphp
                                            <td>
                                                @if($horario)
                                                    <div class="fw-semibold">{{ $horario->turma->nome }}</div>
                                                    <div><small>{{ $horario->materia->nome }}</small></div>
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
                </div>
            </div>
        @endif
    @endforeach
@endif

@endsection
