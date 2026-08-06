{{--
    Espera: $porTurma e $porProfessor (Collections agrupadas por id),
    $idPrefix (string única na página).
    Cada problema já carrega seu próprio 'tipo' ('erro'|'aviso'), então a
    cor/ícone são decididos item a item, não pela lista inteira.
--}}
@php
    $idPrefix = $idPrefix ?? 'inc';
    $corDoTipo = fn ($tipo) => $tipo === 'erro' ? 'danger' : 'warning';
    $iconeDoTipo = fn ($tipo) => $tipo === 'erro' ? 'bi-x-circle' : 'bi-exclamation-triangle';
    $total = $porTurma->flatten(1)->count() + $porProfessor->flatten(1)->count();
@endphp

@if($total > 0)
    <div class="accordion" id="accordion{{ $idPrefix }}">
        @foreach($porTurma as $turmaId => $problemas)
            @php
                $nome = $problemas->first()['turma_nome'];
                $piorTipo = $problemas->contains(fn ($p) => $p['tipo'] === 'erro') ? 'erro' : 'aviso';
                $cor = $corDoTipo($piorTipo);
            @endphp
            <div class="accordion-item mb-2 border border-{{ $cor }}">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $idPrefix }}Turma{{ $turmaId }}">
                        <span class="fw-semibold">{{ $nome }}</span>
                        <span class="badge bg-{{ $cor }} {{ $cor === 'warning' ? 'text-dark' : '' }} ms-2">{{ $problemas->count() }}</span>
                    </button>
                </h2>
                <div id="{{ $idPrefix }}Turma{{ $turmaId }}" class="accordion-collapse collapse" data-bs-parent="#accordion{{ $idPrefix }}">
                    <ul class="list-group list-group-flush">
                        @foreach($problemas as $problema)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                                <span class="d-flex gap-2">
                                    <i class="bi {{ $iconeDoTipo($problema['tipo']) }} text-{{ $corDoTipo($problema['tipo']) }}"></i>
                                    <span>{{ $problema['mensagem'] }}</span>
                                </span>
                                @if($problema['acao_url'])
                                    <a href="{{ $problema['acao_url'] }}" class="btn btn-sm btn-outline-{{ $corDoTipo($problema['tipo']) }} text-nowrap">
                                        {{ $problema['acao_label'] }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach

        @foreach($porProfessor as $professorId => $problemas)
            @php
                $nome = $problemas->first()['professor_nome'];
                $piorTipo = $problemas->contains(fn ($p) => $p['tipo'] === 'erro') ? 'erro' : 'aviso';
                $cor = $corDoTipo($piorTipo);
            @endphp
            <div class="accordion-item mb-2 border border-{{ $cor }}">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $idPrefix }}Prof{{ $professorId }}">
                        <i class="bi bi-person-badge me-2"></i>
                        <span class="fw-semibold">{{ $nome }}</span>
                        <span class="badge bg-{{ $cor }} {{ $cor === 'warning' ? 'text-dark' : '' }} ms-2">{{ $problemas->count() }}</span>
                    </button>
                </h2>
                <div id="{{ $idPrefix }}Prof{{ $professorId }}" class="accordion-collapse collapse" data-bs-parent="#accordion{{ $idPrefix }}">
                    <ul class="list-group list-group-flush">
                        @foreach($problemas as $problema)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                                <span class="d-flex gap-2">
                                    <i class="bi {{ $iconeDoTipo($problema['tipo']) }} text-{{ $corDoTipo($problema['tipo']) }}"></i>
                                    <span>{{ $problema['mensagem'] }}</span>
                                </span>
                                @if($problema['acao_url'])
                                    <a href="{{ $problema['acao_url'] }}" class="btn btn-sm btn-outline-{{ $corDoTipo($problema['tipo']) }} text-nowrap">
                                        {{ $problema['acao_label'] }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endif
