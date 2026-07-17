@php
    $cor = match($status) {
        'pendente'     => 'bg-warning text-dark',
        'em_andamento' => 'bg-primary',
        'concluida'    => 'bg-success',
        'cancelada'    => 'bg-danger',
        'pausada'      => 'bg-secondary',
    };
    $rotulo = match($status) {
        'pendente'     => 'Pendente',
        'em_andamento' => 'Em andamento',
        'concluida'    => 'Concluída',
        'cancelada'    => 'Cancelada',
        'pausada'      => 'Pausada',
    };
@endphp
<span class="badge {{ $cor }}">{{ $rotulo }}</span>
