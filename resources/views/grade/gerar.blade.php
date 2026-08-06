@extends('layouts.app')

@section('titulo', 'Gerar Grade · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Gerar Grade'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Gerar Grade</h1>
        <p class="page-header__subtitle mb-0">Validação de viabilidade antes de gerar</p>
    </div>
</div>

@if($totalProblemas === 0)
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>Nenhum problema encontrado. A grade pode ser gerada.</span>
    </div>
@else
    <div class="card mb-3">
        <div class="card-header bg-danger text-white">
            <strong>{{ $totalProblemas }} problema(s) encontrado(s) — impedem a geração</strong>
            <div class="small fw-normal">Clique numa série/turma ou professor pra ver o detalhe</div>
        </div>
        <div class="card-body">
            @include('grade.partials.inconsistencias', [
                'porTurma' => $problemasPorTurma,
                'porProfessor' => $problemasPorProfessor,
                'idPrefix' => 'gerarProblemas',
            ])
        </div>
    </div>
@endif

<form action="/grade/gerar" method="POST" data-confirm="Gerar a grade agora? Os horários que você editou manualmente serão mantidos — só os gerados automaticamente serão recalculados.">
    @csrf
    <button type="submit" class="btn btn-brand fw-bold" {{ $totalProblemas > 0 ? 'disabled' : '' }}>
        <i class="bi bi-magic"></i> Gerar Grade
    </button>
</form>
<div class="form-text mt-2">
    <i class="bi bi-info-circle"></i> Células que você ajustou manualmente em "Editar Horários" não são alteradas ao gerar de novo.
</div>

@if($totalCobertura > 0)
    <div class="card mt-4">
        <div class="card-header bg-warning">
            <strong>Cobertura da matriz curricular</strong>
            <div class="small fw-normal">Não impede gerar de novo, mas mostra onde a grade atual ficou incompleta ou excedente.</div>
        </div>
        <div class="card-body">
            @include('grade.partials.inconsistencias', [
                'porTurma' => $coberturaPorTurma,
                'porProfessor' => $coberturaPorProfessor,
                'idPrefix' => 'gerarCobertura',
            ])
        </div>
    </div>
@endif

@endsection
