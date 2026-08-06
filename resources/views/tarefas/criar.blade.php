@extends('layouts.app')

@section('titulo', 'Registrar Tarefa · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Tarefas', 'url' => '/tarefas'],
    ['label' => 'Nova tarefa'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Registrar Nova Tarefa</h1>
        <p class="page-header__subtitle mb-0">Descreva o chamado e defina os responsáveis</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="card">
            <div class="card-body">

                <form action="/tarefas/salvar" method="POST">
                    @csrf

                    {{-- Título --}}
                    <div class="form-floating mb-3">
                        <input type="text" id="campoTitulo" name="titulo"
                               class="form-control {{ $errors->has('titulo') ? 'is-invalid' : '' }}"
                               value="{{ old('titulo') }}"
                               placeholder="Resumo da tarefa">
                        <label for="campoTitulo">Título</label>
                        @if($errors->has('titulo'))
                            <div class="invalid-feedback">{{ $errors->first('titulo') }}</div>
                        @endif
                    </div>

                    {{-- Descrição --}}
                    <div class="form-floating mb-3">
                        <textarea name="descricao" id="campoDescricao" rows="4"
                                  class="form-control {{ $errors->has('descricao') ? 'is-invalid' : '' }}"
                                  placeholder="Detalhe o que precisa ser feito" style="height: 110px">{{ old('descricao') }}</textarea>
                        <label for="campoDescricao">Descrição</label>
                        @if($errors->has('descricao'))
                            <div class="invalid-feedback">{{ $errors->first('descricao') }}</div>
                        @endif
                    </div>

                    {{-- Responsáveis --}}
                    <div class="mb-3">
                        <label class="form-label">Responsáveis</label>
                        <select name="responsaveis[]" multiple
                                class="form-select select-responsaveis {{ $errors->has('responsaveis') ? 'is-invalid' : '' }}"
                                data-placeholder="Clique para selecionar os responsáveis">
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}"
                                    {{ in_array($usuario->id, old('responsaveis', [])) ? 'selected' : '' }}>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('responsaveis'))
                            <div class="invalid-feedback d-block">{{ $errors->first('responsaveis') }}</div>
                        @endif
                    </div>

                    {{-- Data a ser feita --}}
                    <div class="form-floating mb-3" style="max-width: 260px;">
                        <input type="date" id="campoData" name="data"
                               class="form-control {{ $errors->has('data') ? 'is-invalid' : '' }}"
                               value="{{ old('data') }}" placeholder="Data">
                        <label for="campoData">Data a ser feita</label>
                        @if($errors->has('data'))
                            <div class="invalid-feedback d-block">{{ $errors->first('data') }}</div>
                        @endif
                    </div>
                    <div class="form-text mb-3 mt-n2">O dia em que essa tarefa deve ser executada. Aparece no calendário do menu.</div>

                    {{-- Observações --}}
                    <div class="form-floating mb-4">
                        <textarea name="observacoes" id="campoObservacoes" rows="2" class="form-control"
                                  placeholder="Opcional" style="height: 80px">{{ old('observacoes') }}</textarea>
                        <label for="campoObservacoes">Observações <span class="text-muted">(opcional)</span></label>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="/tarefas" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-brand fw-bold">
                            <i class="bi bi-check-lg"></i> Registrar Tarefa
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script src="{{ asset('js/tarefas.js') }}"></script>
@endsection
@endsection
