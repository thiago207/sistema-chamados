@extends('layouts.app')

@section('titulo', 'Gerar Grade · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Grade de Horários', 'url' => '/grade'],
    ['label' => 'Gerar Grade'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Gerar Grade</h1>
        <p class="page-header__subtitle mb-0">Validação de viabilidade antes de gerar</p>
    </div>
</div>

@if(session('erro'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-circle"></i>
        <span>{{ session('erro') }}</span>
    </div>
@endif

@if(empty($problemas))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>Nenhum problema encontrado. A grade pode ser gerada.</span>
    </div>
@else
    <div class="card mb-3">
        <div class="card-header bg-danger text-white">
            <strong>{{ count($problemas) }} problema(s) encontrado(s) — impedem a geração</strong>
        </div>
        <ul class="list-group list-group-flush">
            @foreach($problemas as $problema)
                <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                    <span class="d-flex gap-2">
                        <i class="bi bi-x-circle text-danger"></i>
                        <span>{{ $problema['mensagem'] }}</span>
                    </span>
                    @if($problema['acao_url'])
                        <a href="{{ $problema['acao_url'] }}" class="btn btn-sm btn-outline-danger text-nowrap">
                            {{ $problema['acao_label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

<form action="/grade/gerar" method="POST" data-confirm="Gerar a grade agora? Os horários já existentes desta escola serão substituídos.">
    @csrf
    <button type="submit" class="btn btn-brand fw-bold" {{ ! empty($problemas) ? 'disabled' : '' }}>
        <i class="bi bi-magic"></i> Gerar Grade
    </button>
</form>

@if(! empty($cobertura))
    <div class="card mt-4">
        <div class="card-header bg-warning">
            <strong>Cobertura da matriz curricular</strong>
            <div class="small fw-normal">Não impede gerar de novo, mas mostra onde a grade atual ficou incompleta ou excedente.</div>
        </div>
        <ul class="list-group list-group-flush">
            @foreach($cobertura as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                    <span class="d-flex gap-2">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <span>{{ $item['mensagem'] }}</span>
                    </span>
                    @if($item['acao_url'])
                        <a href="{{ $item['acao_url'] }}" class="btn btn-sm btn-outline-warning text-nowrap">
                            {{ $item['acao_label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

@endsection
