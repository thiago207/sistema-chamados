@extends('layouts.guest')

@section('content')
<div class="row justify-content-center w-100">
    <div class="col-md-4">
        <div class="card shadow-lg border-0">
            <div class="card-header text-center bg-white">
                <img src="{{ asset('img/logo.png') }}" alt="Salesianas" style="width: 160px; margin-bottom: 20px;"> 
                <h3 class="fw-bold" style="color: #1a3a6b;">Login</h3>
            </div>
            <div class="card-body p-4 text-center">

                

                <form action="/auth/login" method="POST" class="text-start">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #1a3a6b;">Senha</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold" style="background-color: #c0392b;">
                        Entrar
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection