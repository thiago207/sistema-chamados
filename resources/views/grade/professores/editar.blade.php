@extends('layouts.app')

@section('titulo', 'Editar '.$professor->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores', 'url' => '/grade/professores'],
    ['label' => $professor->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">{{ $professor->nome }}</h1>
        <p class="page-header__subtitle mb-0">
            @if($professor->ativo)
                <span class="badge bg-success">Ativo</span>
            @else
                <span class="badge bg-secondary">Inativo</span>
            @endif
        </p>
    </div>
</div>

<div class="row g-4">
    {{-- Ações rápidas — o que este professor precisa pra entrar na grade --}}
    <div class="col-lg-4 order-lg-2">
        <div class="card mb-3">
            <div class="card-header fw-semibold"><i class="bi bi-sliders me-2 text-brand"></i>Configuração pra grade</div>
            <div class="list-group list-group-flush">
                <a href="/grade/professores/{{ $professor->id }}/vinculos" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-link-45deg text-brand fs-5"></i>
                    <div>
                        <div class="fw-semibold">Vínculos</div>
                        <div class="text-muted small">{{ $professor->vinculos_count }} disciplina(s)/turma(s)</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </a>
                <a href="/grade/professores/{{ $professor->id }}/disponibilidade" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check text-brand fs-5"></i>
                    <div>
                        <div class="fw-semibold">Disponibilidade</div>
                        <div class="text-muted small">{{ $professor->disponibilidades_count }} slot(s) marcado(s)</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </a>
                <a href="/grade/horarios/professor/{{ $professor->id }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-calendar3 text-brand fs-5"></i>
                    <div>
                        <div class="fw-semibold">Grade individual</div>
                        <div class="text-muted small">Ver os horários já gerados</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </a>
            </div>
        </div>

        @if($professor->vinculos_count === 0)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Esse professor ainda não tem nenhum vínculo — comece por aí.
            </div>
        @elseif($professor->disponibilidades_count === 0)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Tem vínculos mas nenhuma disponibilidade marcada — a grade não vai conseguir alocar as aulas dele.
            </div>
        @endif
    </div>

    {{-- Dados cadastrais --}}
    <div class="col-lg-8 order-lg-1">
        <div class="card">
            <div class="card-body p-4">
                <form action="/grade/professores/{{ $professor->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('grade.professores.partials.formulario', ['professor' => $professor])

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-brand fw-bold">
                            <i class="bi bi-check-lg"></i> Salvar
                        </button>
                        <a href="/grade/professores" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
