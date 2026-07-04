@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow-sm border-0">
            <div class="card-header text-white fw-bold" style="background-color: #1a3a6b;">
                Cadastrar Novo Usuário
            </div>
            <div class="card-body p-4">

                {{-- Mensagem de sucesso --}}
                @if(session('sucesso'))
                    <div class="alert alert-success">{{ session('sucesso') }}</div>
                @endif

                <form action="/usuarios/cadastrar" method="POST">
                    @csrf

                    {{-- Nome --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Nome</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Nome completo"
                        >
                        @if($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="email@exemplo.com"
                        >
                        @if($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    {{-- Senha --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Senha</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Mínimo 8 caracteres"
                        >
                        @if($errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    {{-- Confirmar Senha --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Confirmar Senha</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Repita a senha"
                        >
                    </div>

                    <button type="submit" class="btn w-100 text-white fw-bold" style="background-color: #c0392b;">
                        Cadastrar
                    </button>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection