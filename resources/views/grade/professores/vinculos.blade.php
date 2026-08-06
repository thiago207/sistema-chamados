@extends('layouts.app')

@section('titulo', 'Vínculos · '.$professor->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores', 'url' => '/grade/professores'],
    ['label' => $professor->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Vínculos — {{ $professor->nome }}</h1>
        <p class="page-header__subtitle mb-0">O que este professor leciona e para quem</p>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

@if($errors->has('materia_id'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-circle"></i>
        <span>{{ $errors->first('materia_id') }}</span>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form action="/grade/professores/{{ $professor->id }}/vinculos" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Série/Turma</label>
                <select name="turma_id" id="selectTurma" class="form-select" required>
                    <option value="" selected disabled>Selecione...</option>
                    @foreach($turmas as $turma)
                        <option value="{{ $turma->id }}">{{ $turma->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Matéria</label>
                <select name="materia_id" id="selectMateria" class="form-select" required disabled>
                    <option value="" selected disabled>Selecione a turma primeiro</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-brand w-100 fw-bold">
                    <i class="bi bi-plus-lg"></i> Adicionar
                </button>
            </div>
        </form>
    </div>
</div>

@if($professor->vinculos->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-link-45deg"></i>
                <strong>Nenhum vínculo cadastrado</strong>
                Adicione turma + matéria para este professor.
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
                            <th class="ps-4">Turma</th>
                            <th>Matéria</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($professor->vinculos as $vinculo)
                            <tr>
                                <td class="ps-4">{{ $vinculo->turma->nome }}</td>
                                <td>{{ $vinculo->materia->nome }}</td>
                                <td class="pe-4">
                                    <form action="/grade/vinculos/{{ $vinculo->id }}" method="POST" class="d-flex justify-content-end"
                                        data-confirm="Remover este vínculo?" data-confirm-tipo="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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

@section('scripts')
    <script src="{{ asset('js/vinculos.js') }}"></script>
@endsection
