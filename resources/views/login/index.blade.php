@extends('layouts.guest')

@section('titulo', 'Entrar · Sale Marketing')

@section('content')
<div class="row justify-content-center w-100">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="Salesianas" style="width: 120px;" class="mb-3">
                    <h3 class="fw-bold text-brand mb-1">Bem-vindo de volta</h3>
                    <p class="text-muted mb-0 small">Entre com suas credenciais para continuar</p>
                </div>

                @if(session('erro'))
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>{{ session('erro') }}</span>
                    </div>
                @endif

                @if(session('sucesso'))
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('sucesso') }}</span>
                    </div>
                @endif

                <form action="/auth/login" method="POST" class="text-start">
                    @csrf

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <div class="form-floating">
                            <input type="email" id="campoEmail" name="email" class="form-control" placeholder="seu@email.com" required>
                            <label for="campoEmail">Email</label>
                        </div>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <div class="form-floating">
                            <input type="password" id="campoSenha" name="password" class="form-control" placeholder="Senha" required>
                            <label for="campoSenha">Senha</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 fw-bold py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

            </div>
        </div>
        <p class="text-center text-white-50 small mt-4 mb-0">Sale Marketing</p>
    </div>
</div>
@endsection
