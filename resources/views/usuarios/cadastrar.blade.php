@extends('layouts.app')

@section('titulo', 'Cadastrar Usuário · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Cadastro', 'url' => '/usuarios/listar'],
    ['label' => 'Novo usuário'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Cadastrar Novo Usuário</h1>
        <p class="page-header__subtitle mb-0">Adicione um novo usuário ao sistema</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Mensagem de sucesso --}}
        @if(session('sucesso'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i>
                <span>{{ session('sucesso') }}</span>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-4">

                <form action="/usuarios/cadastrar" method="POST">
                    @csrf

                    {{-- Nome --}}
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
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
                        <label class="form-label">Email</label>
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
                        <label class="form-label">Senha</label>
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
                    <div class="mb-4">
                        <label class="form-label">Confirmar Senha</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Repita a senha"
                        >
                    </div>

                    <button type="submit" class="btn btn-brand w-100 fw-bold">
                        <i class="bi bi-person-check"></i> Cadastrar
                    </button>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
