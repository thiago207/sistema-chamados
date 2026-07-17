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

        @if(session('sucesso'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i>
                <span>{{ session('sucesso') }}</span>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-4">

                <form action="/tarefas/salvar" method="POST">
                    @csrf

                    {{-- Título --}}
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo"
                               class="form-control {{ $errors->has('titulo') ? 'is-invalid' : '' }}"
                               value="{{ old('titulo') }}"
                               placeholder="Resumo da tarefa">
                        @if($errors->has('titulo'))
                            <div class="invalid-feedback">{{ $errors->first('titulo') }}</div>
                        @endif
                    </div>

                    {{-- Descrição --}}
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" rows="4"
                                  class="form-control {{ $errors->has('descricao') ? 'is-invalid' : '' }}"
                                  placeholder="Detalhe o que precisa ser feito">{{ old('descricao') }}</textarea>
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

                    <div class="row">
                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="pendente"     {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="em_andamento" {{ old('status') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                                <option value="pausada"      {{ old('status') == 'pausada' ? 'selected' : '' }}>Pausada</option>
                                <option value="concluida"    {{ old('status') == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                <option value="cancelada"    {{ old('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>

                        {{-- Prazo --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prazo</label>
                            <input type="date" name="prazo"
                                   class="form-control {{ $errors->has('prazo') ? 'is-invalid' : '' }}"
                                   value="{{ old('prazo') }}">
                            @if($errors->has('prazo'))
                                <div class="invalid-feedback">{{ $errors->first('prazo') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div class="mb-4">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" rows="2" class="form-control"
                                  placeholder="Opcional">{{ old('observacoes') }}</textarea>
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
