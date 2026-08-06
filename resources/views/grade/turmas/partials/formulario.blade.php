@php
    $diasSelecionados = old('dias_semana', $turma->dias_semana ?? []);
    $diasNomes = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];
    $temManha = old('tem_manha', $turma && $turma->aulas_manha > 0);
    $temTarde = old('tem_tarde', $turma && $turma->aulas_tarde > 0);
@endphp

@if($errors->any())
    <div class="alert alert-danger d-flex gap-2">
        <i class="bi bi-exclamation-circle-fill mt-1"></i>
        <div>
            <strong>Não foi possível salvar — corrija o que está destacado abaixo:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $mensagem)
                    <li>{{ $mensagem }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="form-floating mb-4">
    <input type="text" id="campoNome" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
        placeholder="Ex: 6º Ano, 6º Ano A, 3ª Série do Ensino Médio..." value="{{ old('nome', $turma->nome ?? '') }}" required>
    <label for="campoNome">Nome da série/turma</label>
    @if($errors->has('nome'))
        <div class="invalid-feedback">{{ $errors->first('nome') }}</div>
    @endif
    <div class="form-text">Use só o nome da série (ex: "6º Ano") quando houver uma única turma, ou acrescente a letra (ex: "6º Ano A", "6º Ano B") quando houver mais de uma.</div>
</div>

<div class="mb-4">
    <label class="form-label d-block fw-semibold">Dias da semana</label>
    <div class="d-flex flex-wrap gap-2">
        @foreach($diasNomes as $numero => $nome)
            <input type="checkbox" class="btn-check {{ $errors->has('dias_semana') ? 'is-invalid' : '' }}" name="dias_semana[]" value="{{ $numero }}"
                id="dia{{ $numero }}" autocomplete="off" {{ in_array($numero, $diasSelecionados) ? 'checked' : '' }}>
            <label class="btn btn-outline-primary btn-sm rounded-pill px-3" for="dia{{ $numero }}">{{ $nome }}</label>
        @endforeach
    </div>
    @if($errors->has('dias_semana'))
        <div class="text-danger small mt-2">{{ $errors->first('dias_semana') }}</div>
    @endif
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="border rounded-3 p-3 h-100">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input campo-turno" type="checkbox" role="switch" name="tem_manha" value="1"
                    id="temManha" data-alvo=".campos-manha" {{ $temManha ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="temManha"><i class="bi bi-sunrise me-1"></i> Tem aula de manhã</label>
            </div>
            <div class="campos-manha campos-turno" style="{{ $temManha ? '' : 'display:none' }}">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="number" name="aulas_manha" id="aulasManha" class="form-control {{ $errors->has('aulas_manha') ? 'is-invalid' : '' }}" min="1" max="20" placeholder="Qtd." value="{{ old('aulas_manha', $turma->aulas_manha ?? '') }}">
                            <label for="aulasManha">Qtd. de aulas</label>
                            @if($errors->has('aulas_manha'))
                                <div class="invalid-feedback">{{ $errors->first('aulas_manha') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" name="inicio_manha" id="inicioManha" class="form-control campo-inicio-manha {{ $errors->has('inicio_manha') ? 'is-invalid' : '' }}" placeholder="Início" value="{{ old('inicio_manha', $turma && $turma->inicio_manha ? \Illuminate\Support\Carbon::parse($turma->inicio_manha)->format('H:i') : '') }}">
                            <label for="inicioManha">Início da 1ª aula</label>
                            @if($errors->has('inicio_manha'))
                                <div class="invalid-feedback">{{ $errors->first('inicio_manha') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-text mt-2 mb-0">Término previsto: <span class="termino-manha fw-semibold">—</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-3 p-3 h-100">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input campo-turno" type="checkbox" role="switch" name="tem_tarde" value="1"
                    id="temTarde" data-alvo=".campos-tarde" {{ $temTarde ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="temTarde"><i class="bi bi-sunset me-1"></i> Tem aula de tarde</label>
            </div>
            <div class="campos-tarde campos-turno" style="{{ $temTarde ? '' : 'display:none' }}">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="number" name="aulas_tarde" id="aulasTarde" class="form-control {{ $errors->has('aulas_tarde') ? 'is-invalid' : '' }}" min="1" max="20" placeholder="Qtd." value="{{ old('aulas_tarde', $turma->aulas_tarde ?? '') }}">
                            <label for="aulasTarde">Qtd. de aulas</label>
                            @if($errors->has('aulas_tarde'))
                                <div class="invalid-feedback">{{ $errors->first('aulas_tarde') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" name="inicio_tarde" id="inicioTarde" class="form-control campo-inicio-tarde {{ $errors->has('inicio_tarde') ? 'is-invalid' : '' }}" placeholder="Início" value="{{ old('inicio_tarde', $turma && $turma->inicio_tarde ? \Illuminate\Support\Carbon::parse($turma->inicio_tarde)->format('H:i') : '') }}">
                            <label for="inicioTarde">Início da 1ª aula</label>
                            @if($errors->has('inicio_tarde'))
                                <div class="invalid-feedback">{{ $errors->first('inicio_tarde') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-text mt-2 mb-0">Término previsto: <span class="termino-tarde fw-semibold">—</span></div>
            </div>
        </div>
    </div>
</div>

<div class="form-floating mb-2" style="max-width: 320px;">
    <input type="number" name="duracao_minutos" id="campoDuracao" class="form-control {{ $errors->has('duracao_minutos') ? 'is-invalid' : '' }}" min="10" max="180" placeholder="Duração" value="{{ old('duracao_minutos', $turma->duracao_minutos ?? 50) }}" required>
    <label for="campoDuracao">Duração de cada aula (minutos)</label>
    @if($errors->has('duracao_minutos'))
        <div class="invalid-feedback">{{ $errors->first('duracao_minutos') }}</div>
    @endif
</div>
