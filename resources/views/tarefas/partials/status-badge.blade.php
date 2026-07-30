@php
    $cor = match($status) {
        'pendente'  => 'bg-warning text-dark',
        'concluida' => 'bg-success',
        'cancelada' => 'bg-danger',
    };
    $rotulo = match($status) {
        'pendente'  => 'Pendente',
        'concluida' => 'Concluída',
        'cancelada' => 'Cancelada',
    };
@endphp
<span class="badge {{ $cor }}">{{ $rotulo }}</span>
