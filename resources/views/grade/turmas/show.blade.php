@extends('layouts.app')

@section('titulo', $turma->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Séries/Turmas', 'url' => '/grade/turmas'],
    ['label' => $turma->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">{{ $turma->nome }}</h1>
        <p class="page-header__subtitle mb-0">Pré-visualização dos horários que essa configuração vai gerar</p>
    </div>
    <a href="/grade/turmas/{{ $turma->id }}/editar" class="btn btn-brand">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>

@php
    $diasNomes = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];
    $dias = collect($turma->dias_semana)->sort()->values();
@endphp

@if($turma->materias->isNotEmpty())
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-journal-bookmark me-2 text-brand"></i>Matriz curricular</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Disciplina</th>
                        <th>Aulas/semana</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($turma->materias as $materia)
                        <tr>
                            <td class="ps-4">{{ $materia->nome }}</td>
                            <td>{{ $materia->pivot->quantidade_aulas }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@foreach(['manha' => 'Manhã', 'tarde' => 'Tarde'] as $turno => $rotulo)
    @php $aulas = $turno === 'manha' ? $turma->aulas_manha : $turma->aulas_tarde; @endphp
    @if($aulas > 0)
        <div class="card mb-3">
            <div class="card-header fw-semibold"><i class="bi bi-{{ $turno === 'manha' ? 'sunrise' : 'sunset' }} me-2 text-brand"></i>{{ $rotulo }}</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center align-middle">
                        <thead>
                            <tr>
                                <th>{{ $rotulo }}</th>
                                @foreach($dias as $dia)
                                    <th>{{ $diasNomes[$dia] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($numero = 1; $numero <= $aulas; $numero++)
                                <tr>
                                    <td class="fw-semibold text-muted"><small>{{ $turma->horarioDoSlot($turno, $numero) }}</small></td>
                                    @foreach($dias as $dia)
                                        <td class="text-muted"><small>—</small></td>
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

@endsection
