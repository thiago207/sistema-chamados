@extends('layouts.app')

@section('titulo', 'Novo Professor · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Professores', 'url' => '/grade/professores'],
    ['label' => 'Novo'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Novo Professor</h1>
        <p class="page-header__subtitle mb-0">Depois de salvar, você vincula as disciplinas/turmas e marca a disponibilidade</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form action="/grade/professores" method="POST">
                    @csrf
                    @include('grade.professores.partials.formulario', ['professor' => null])

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-brand fw-bold">
                            <i class="bi bi-check-lg"></i> Salvar e continuar
                        </button>
                        <a href="/grade/professores" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
