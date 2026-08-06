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

<div class="input-group mb-3">
    <span class="input-group-text"><i class="bi bi-person"></i></span>
    <div class="form-floating">
        <input type="text" id="campoNome" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
            value="{{ old('nome', $professor->nome ?? '') }}" placeholder="Nome completo" required autofocus>
        <label for="campoNome">Nome completo</label>
        @if($errors->has('nome'))
            <div class="invalid-feedback">{{ $errors->first('nome') }}</div>
        @endif
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <div class="form-floating">
                <input type="email" id="campoEmail" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email', $professor->email ?? '') }}" placeholder="email@exemplo.com">
                <label for="campoEmail">Email <span class="text-muted">(opcional)</span></label>
                @if($errors->has('email'))
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <div class="form-floating">
                <input type="text" id="campoTelefone" name="telefone" class="form-control {{ $errors->has('telefone') ? 'is-invalid' : '' }}"
                    value="{{ old('telefone', $professor->telefone ?? '') }}" placeholder="(00) 00000-0000">
                <label for="campoTelefone">Telefone <span class="text-muted">(opcional)</span></label>
                @if($errors->has('telefone'))
                    <div class="invalid-feedback">{{ $errors->first('telefone') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="form-check form-switch mb-2">
    <input class="form-check-input" type="checkbox" role="switch" name="ativo" value="1" id="ativo"
        {{ old('ativo', $professor->ativo ?? true) ? 'checked' : '' }}>
    <label class="form-check-label fw-semibold" for="ativo">Ativo</label>
    <div class="form-text mb-0">Professor inativo sai da geração de grade sem perder o histórico.</div>
</div>
