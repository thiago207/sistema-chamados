@extends('layouts.app')

@section('titulo', 'Horários · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Horários'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Horários</h1>
        <p class="page-header__subtitle mb-0">Visualização e ajuste manual da grade gerada</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/grade" class="btn btn-outline-secondary">
            <i class="bi bi-grid-3x3"></i> Visão Geral
        </a>
        @if($turma)
            <a href="/grade/exportar/pdf/turma/{{ $turma->id }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf"></i> PDF desta turma
            </a>
        @endif
        <a href="/grade/exportar/pdf/geral" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-pdf"></i> PDF geral
        </a>
        <a href="/grade/exportar/excel" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
    </div>
</div>

@if($errors->has('materia_id'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-circle"></i>
        <span>{{ $errors->first('materia_id') }}</span>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form action="/grade/horarios" method="GET" class="d-flex gap-2 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label">Turma</label>
                <select name="turma_id" class="form-select" onchange="this.form.submit()">
                    @foreach($turmas as $t)
                        <option value="{{ $t->id }}" {{ $turma && $turma->id === $t->id ? 'selected' : '' }}>
                            {{ $t->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if(! $turma)
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <strong>Nenhuma turma cadastrada</strong>
            </div>
        </div>
    </div>
@else
    @php
        $diasNomes = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
        $dias = collect($turma->dias_semana)->sort()->values();
        $temErro = $problemasDaTurma->contains(fn ($p) => $p['tipo'] === 'erro');
        $urlAtual = "/grade/horarios?turma_id={$turma->id}";
    @endphp

    @if($problemasDaTurma->isNotEmpty())
        <div class="card mb-3 border {{ $temErro ? 'border-danger' : 'border-warning' }}">
            <div class="card-header fw-semibold {{ $temErro ? 'bg-danger text-white' : 'bg-warning' }}">
                {{ $problemasDaTurma->count() }} pendência(s) para {{ $turma->nome }}
            </div>
            <ul class="list-group list-group-flush">
                @foreach($problemasDaTurma as $problema)
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                        <span class="d-flex gap-2">
                            <i class="bi {{ $problema['tipo'] === 'erro' ? 'bi-x-circle text-danger' : 'bi-exclamation-triangle text-warning' }}"></i>
                            <span>{{ $problema['mensagem'] }}</span>
                        </span>
                        @if($problema['acao_url'] && $problema['acao_url'] !== $urlAtual)
                            <a href="{{ $problema['acao_url'] }}" class="btn btn-sm btn-outline-{{ $problema['tipo'] === 'erro' ? 'danger' : 'warning' }} text-nowrap">
                                {{ $problema['acao_label'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle"></i>
            <span>Nenhuma pendência para {{ $turma->nome }}.</span>
        </div>
    @endif

    @foreach(['manha' => ['Manhã', $turma->aulas_manha], 'tarde' => ['Tarde', $turma->aulas_tarde]] as $turno => [$rotulo, $max])
        @if($max > 0)
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-{{ $turno === 'manha' ? 'sunrise' : 'sunset' }} me-2 text-brand"></i>{{ $rotulo }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 align-middle">
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
                                        <td class="text-muted"><small>{{ $turma->horarioDoSlot($turno, $numero) }}</small></td>
                                        @foreach($dias as $dia)
                                            @php $horario = $grade[$turno][$numero][$dia] ?? null; @endphp
                                            <td>
                                                <button type="button" class="btn btn-sm w-100 position-relative {{ $horario ? 'btn-outline-primary' : 'btn-outline-secondary' }} btn-celula"
                                                    data-dia="{{ $dia }}" data-turno="{{ $turno }}" data-numero="{{ $numero }}"
                                                    data-titulo="{{ $diasNomes[$dia] }} · {{ $turma->horarioDoSlot($turno, $numero) }}">
                                                    @if($horario)
                                                        @if($horario->editado_manualmente)
                                                            <i class="bi bi-pin-angle-fill position-absolute top-0 end-0 mt-1 me-1 text-warning" title="Editado manualmente — não é alterado ao gerar a grade de novo" style="font-size: .7rem;"></i>
                                                        @endif
                                                        <div class="fw-semibold">{{ $horario->materia->nome }}</div>
                                                        <div><small>{{ $horario->professor->nome }}</small></div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </button>
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

    {{-- Modal compartilhado de edição de célula --}}
    <div class="modal fade" id="modalCelula" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-brand text-white">
                    <h5 class="modal-title" id="modalCelulaTitulo">Editar aula</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/grade/horarios/celula" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="turma_id" value="{{ $turma->id }}">
                        <input type="hidden" name="dia_semana" id="campoDia">
                        <input type="hidden" name="turno" id="campoTurno">
                        <input type="hidden" name="numero_aula" id="campoNumero">

                        <div class="mb-3">
                            <label class="form-label">Matéria</label>
                            <select name="materia_id" id="selectOpcoesCelula" class="form-select">
                                <option value="">— remover aula deste horário —</option>
                            </select>
                            <div class="form-text">Só aparecem matérias com aulas ainda não alocadas e cujo professor está livre nesse horário.</div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-brand fw-bold">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
    @if($turma)
        <script>
            window.opcoesPorSlot = @json($opcoesPorSlot);
        </script>
        <script src="{{ asset('js/horarios.js') }}"></script>
    @endif
@endsection
