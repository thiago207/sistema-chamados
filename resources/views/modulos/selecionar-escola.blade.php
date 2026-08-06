@extends('layouts.app')

@section('titulo', 'Selecionar Escola · Sale Marketing')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Selecionar Escola</h1>
        <p class="page-header__subtitle mb-0">Escolha a escola em que você quer trabalhar na Grade de Horários</p>
    </div>
</div>

@if($escolas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-building"></i>
                <strong>Nenhuma escola cadastrada</strong>
                Cadastre uma escola para começar a usar o módulo Grade.
            </div>
        </div>
    </div>
@else
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="/grade/escola/selecionar" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Escola</label>
                            <select name="escola_id" class="form-select" required>
                                <option value="" selected disabled>Selecione...</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-brand w-100 fw-bold">
                            <i class="bi bi-check-lg"></i> Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
