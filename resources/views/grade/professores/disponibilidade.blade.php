@extends('layouts.app')

@section('titulo', 'Disponibilidade · '.$professor->nome.' · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores', 'url' => '/grade/professores'],
    ['label' => $professor->nome],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Disponibilidade — {{ $professor->nome }}</h1>
        <p class="page-header__subtitle mb-0">Clique nos quadradinhos para marcar os horários em que ele pode dar aula</p>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

<div class="alert" id="alertaResumo">
    Precisa dar <strong>{{ $aulasNecessarias }}</strong> aula(s) por semana · marcou <strong id="totalMarcados">{{ count($slotsMarcados) }}</strong> slot(s) disponível(is)
</div>

@php
    $diasNomes = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
@endphp

<form action="/grade/professores/{{ $professor->id }}/disponibilidade" method="POST">
    @csrf
    @method('PUT')

    <div class="d-flex gap-2 mb-3">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnMarcarTudo">Marcar tudo</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimparTudo">Limpar tudo</button>
    </div>

    @foreach(['manha' => ['Manhã', $maxManha], 'tarde' => ['Tarde', $maxTarde]] as $turno => [$rotulo, $max])
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $rotulo }}</strong>
                <button type="button" class="btn btn-outline-success btn-sm btn-marcar-turno" data-turno="{{ $turno }}">
                    Marcar {{ $rotulo }} inteira
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                @foreach($diasNomes as $dia => $nome)
                                    <th>
                                        {{ $nome }}<br>
                                        <button type="button" class="btn btn-link btn-sm p-0 btn-marcar-dia" data-dia="{{ $dia }}">marcar dia</button>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($numero = 1; $numero <= $max; $numero++)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $numero }}ª</td>
                                    @foreach($diasNomes as $dia => $nome)
                                        @php
                                            $valor = "{$dia}_{$turno}_{$numero}";
                                            $id = "slot_{$valor}";
                                        @endphp
                                        <td>
                                            <input type="checkbox" class="btn-check slot-checkbox" name="slots[]" value="{{ $valor }}"
                                                id="{{ $id }}" data-dia="{{ $dia }}" data-turno="{{ $turno }}"
                                                {{ in_array($valor, $slotsMarcados) ? 'checked' : '' }} autocomplete="off">
                                            <label class="btn btn-outline-success" for="{{ $id }}">
                                                <i class="bi bi-check-lg"></i>
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn btn-brand fw-bold">
        <i class="bi bi-check-lg"></i> Salvar Disponibilidade
    </button>
</form>

@endsection

@section('scripts')
    <script src="{{ asset('js/disponibilidade.js') }}"></script>
@endsection
