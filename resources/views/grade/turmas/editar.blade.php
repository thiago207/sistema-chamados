@extends('layouts.app')

@section('titulo', 'Editar '.$turma->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Séries/Turmas', 'url' => '/grade/turmas'],
    ['label' => $turma->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Editar {{ $turma->nome }}</h1>
        <p class="page-header__subtitle mb-0">Configure o turno, os dias e a matriz curricular</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form action="/grade/turmas/{{ $turma->id }}" method="POST">
            @csrf
            @method('PUT')

            @include('grade.turmas.partials.formulario', ['turma' => $turma])
            @include('grade.turmas.partials.materias', ['turma' => $turma, 'materias' => $materias])

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-brand fw-bold">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
                <a href="/grade/turmas" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ asset('js/turmas.js') }}"></script>
@endsection
