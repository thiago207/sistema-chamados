@php
    $diasSelecionados = old('dias_semana', $turma->dias_semana ?? []);
    $diasNomes = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];
    $temManha = old('tem_manha', $turma && $turma->aulas_manha > 0);
    $temTarde = old('tem_tarde', $turma && $turma->aulas_tarde > 0);
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Não foi possível salvar — corrija o que está destacado abaixo:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $mensagem)
                <li>{{ $mensagem }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Nome da série/turma</label>
    <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
        placeholder="Ex: 6º Ano, 6º Ano A, 3ª Série do Ensino Médio..." value="{{ old('nome', $turma->nome ?? '') }}" required>
    @if($errors->has('nome'))
        <div class="invalid-feedback">{{ $errors->first('nome') }}</div>
    @endif
    <div class="form-text">Use só o nome da série (ex: "6º Ano") quando houver uma única turma, ou acrescente a letra (ex: "6º Ano A", "6º Ano B") quando houver mais de uma.</div>
</div>

<div class="mb-3">
    <label class="form-label d-block">Dias da semana</label>
    <div class="d-flex flex-wrap gap-3">
        @foreach($diasNomes as $numero => $nome)
            <div class="form-check">
                <input class="form-check-input {{ $errors->has('dias_semana') ? 'is-invalid' : '' }}" type="checkbox" name="dias_semana[]" value="{{ $numero }}"
                    id="dia{{ $numero }}" {{ in_array($numero, $diasSelecionados) ? 'checked' : '' }}>
                <label class="form-check-label" for="dia{{ $numero }}">{{ $nome }}</label>
            </div>
        @endforeach
    </div>
    @if($errors->has('dias_semana'))
        <div class="text-danger small mt-1">{{ $errors->first('dias_semana') }}</div>
    @endif
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input class="form-check-input campo-turno" type="checkbox" name="tem_manha" value="1"
                id="temManha" data-alvo=".campos-manha" {{ $temManha ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="temManha">Tem aula de manhã</label>
        </div>
        <div class="campos-manha campos-turno" style="{{ $temManha ? '' : 'display:none' }}">
            <label class="form-label">Quantidade de aulas</label>
            <input type="number" name="aulas_manha" class="form-control mb-2 {{ $errors->has('aulas_manha') ? 'is-invalid' : '' }}" min="1" max="20" value="{{ old('aulas_manha', $turma->aulas_manha ?? '') }}">
            @if($errors->has('aulas_manha'))
                <div class="invalid-feedback">{{ $errors->first('aulas_manha') }}</div>
            @endif
            <label class="form-label">Início da 1ª aula</label>
            <input type="time" name="inicio_manha" class="form-control campo-inicio-manha {{ $errors->has('inicio_manha') ? 'is-invalid' : '' }}" value="{{ old('inicio_manha', $turma && $turma->inicio_manha ? \Illuminate\Support\Carbon::parse($turma->inicio_manha)->format('H:i') : '') }}">
            @if($errors->has('inicio_manha'))
                <div class="invalid-feedback">{{ $errors->first('inicio_manha') }}</div>
            @endif
            <div class="form-text">Término previsto: <span class="termino-manha">—</span></div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input class="form-check-input campo-turno" type="checkbox" name="tem_tarde" value="1"
                id="temTarde" data-alvo=".campos-tarde" {{ $temTarde ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="temTarde">Tem aula de tarde</label>
        </div>
        <div class="campos-tarde campos-turno" style="{{ $temTarde ? '' : 'display:none' }}">
            <label class="form-label">Quantidade de aulas</label>
            <input type="number" name="aulas_tarde" class="form-control mb-2 {{ $errors->has('aulas_tarde') ? 'is-invalid' : '' }}" min="1" max="20" value="{{ old('aulas_tarde', $turma->aulas_tarde ?? '') }}">
            @if($errors->has('aulas_tarde'))
                <div class="invalid-feedback">{{ $errors->first('aulas_tarde') }}</div>
            @endif
            <label class="form-label">Início da 1ª aula</label>
            <input type="time" name="inicio_tarde" class="form-control campo-inicio-tarde {{ $errors->has('inicio_tarde') ? 'is-invalid' : '' }}" value="{{ old('inicio_tarde', $turma && $turma->inicio_tarde ? \Illuminate\Support\Carbon::parse($turma->inicio_tarde)->format('H:i') : '') }}">
            @if($errors->has('inicio_tarde'))
                <div class="invalid-feedback">{{ $errors->first('inicio_tarde') }}</div>
            @endif
            <div class="form-text">Término previsto: <span class="termino-tarde">—</span></div>
        </div>
    </div>
</div>

<div class="mb-2">
    <label class="form-label">Duração de cada aula (minutos)</label>
    <input type="number" name="duracao_minutos" id="campoDuracao" class="form-control {{ $errors->has('duracao_minutos') ? 'is-invalid' : '' }}" min="10" max="180" value="{{ old('duracao_minutos', $turma->duracao_minutos ?? 50) }}" required>
    @if($errors->has('duracao_minutos'))
        <div class="invalid-feedback">{{ $errors->first('duracao_minutos') }}</div>
    @endif
</div>
