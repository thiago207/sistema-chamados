@extends('layouts.app')

@section('titulo', 'Grade de Horários · Sale Marketing')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header__title">Grade de Horários</h1>
        <p class="page-header__subtitle mb-0">
            @if($escola)
                {{ $escola->nome }}
            @endif
        </p>
    </div>
</div>

<div class="row g-3">
    @php
        $cards = [
            ['icone' => 'bi-journal-bookmark', 'titulo' => 'Matérias', 'url' => '/grade/materias'],
            ['icone' => 'bi-easel', 'titulo' => 'Séries/Turmas', 'url' => '/grade/turmas'],
            ['icone' => 'bi-person-badge', 'titulo' => 'Professores', 'url' => '/grade/professores'],
            ['icone' => 'bi-magic', 'titulo' => 'Gerar Grade', 'url' => '/grade/gerar'],
            ['icone' => 'bi-calendar3', 'titulo' => 'Horários', 'url' => '/grade/horarios'],
            ['icone' => 'bi-grid-3x3', 'titulo' => 'Visão Geral', 'url' => '/grade/horarios/geral'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-6 col-md-4">
            <a href="{{ $card['url'] }}" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi {{ $card['icone'] }} display-5 text-brand mb-2 d-block"></i>
                        <span class="fw-semibold">{{ $card['titulo'] }}</span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

@endsection
