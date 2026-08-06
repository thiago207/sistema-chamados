@extends('layouts.app')

@section('titulo', 'Cadastrar Usuário · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Usuários', 'url' => '/usuarios/listar'],
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

        <div class="card">
            <div class="card-body p-4">

                <form action="/usuarios/cadastrar" method="POST">
                    @csrf

                    {{-- Nome --}}
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            id="campoNome"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Nome completo"
                        >
                        <label for="campoNome">Nome completo</label>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="form-floating mb-3">
                        <input
                            type="email"
                            id="campoEmail"
                            name="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="email@exemplo.com"
                        >
                        <label for="campoEmail">Email</label>
                        @if($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    {{-- Senha --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input
                                    type="password"
                                    id="campoSenha"
                                    name="password"
                                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    placeholder="Mínimo 8 caracteres"
                                >
                                <label for="campoSenha">Senha</label>
                                @if($errors->has('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Confirmar Senha --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input
                                    type="password"
                                    id="campoConfirmarSenha"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Repita a senha"
                                >
                                <label for="campoConfirmarSenha">Confirmar senha</label>
                            </div>
                        </div>
                    </div>

                    {{-- Papel --}}
                    <div class="form-floating mb-3">
                        <select id="campoPapel" name="papel" class="form-select campo-papel {{ $errors->has('papel') ? 'is-invalid' : '' }}">
                            <option value="tarefas" {{ old('papel') === 'tarefas' ? 'selected' : '' }}>Tarefas</option>
                            <option value="grade" {{ old('papel') === 'grade' ? 'selected' : '' }}>Grade de Horários</option>
                            <option value="master" {{ old('papel') === 'master' ? 'selected' : '' }}>Master (acesso total)</option>
                        </select>
                        <label for="campoPapel">Papel</label>
                        @if($errors->has('papel'))
                            <div class="invalid-feedback">{{ $errors->first('papel') }}</div>
                        @endif
                    </div>

                    {{-- Escola — só faz sentido pro papel "grade" --}}
                    <div class="form-floating mb-4 grupo-escola" style="display: none;">
                        <select id="campoEscola" name="escola_id" class="form-select {{ $errors->has('escola_id') ? 'is-invalid' : '' }}">
                            <option value="" selected disabled>Selecione...</option>
                            @foreach($escolas as $escola)
                                <option value="{{ $escola->id }}" {{ old('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                            @endforeach
                        </select>
                        <label for="campoEscola">Escola</label>
                        @if($errors->has('escola_id'))
                            <div class="invalid-feedback">{{ $errors->first('escola_id') }}</div>
                        @endif
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
