@php
    $linksSubnav = [
        ['url' => '/grade', 'icone' => 'bi-house', 'label' => 'Início', 'ativo' => request()->is('grade')],
        ['url' => '/grade/materias', 'icone' => 'bi-journal-bookmark', 'label' => 'Matérias', 'ativo' => request()->is('grade/materias*')],
        ['url' => '/grade/turmas', 'icone' => 'bi-easel', 'label' => 'Séries/Turmas', 'ativo' => request()->is('grade/turmas*')],
        ['url' => '/grade/professores', 'icone' => 'bi-person-badge', 'label' => 'Professores', 'ativo' => request()->is('grade/professores*')],
        ['url' => '/grade/gerar', 'icone' => 'bi-magic', 'label' => 'Gerar Grade', 'ativo' => request()->is('grade/gerar')],
        ['url' => '/grade/horarios', 'icone' => 'bi-calendar3', 'label' => 'Editar Horários', 'ativo' => request()->is('grade/horarios')],
    ];
@endphp

<ul class="nav nav-pills grade-subnav mb-4">
    @foreach($linksSubnav as $link)
        <li class="nav-item">
            <a class="nav-link {{ $link['ativo'] ? 'active' : '' }}" href="{{ $link['url'] }}">
                <i class="bi {{ $link['icone'] }}"></i> {{ $link['label'] }}
            </a>
        </li>
    @endforeach
</ul>
