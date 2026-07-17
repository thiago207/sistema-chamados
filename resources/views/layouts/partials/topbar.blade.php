<div class="topbar">
    <button class="btn-toggle" id="btnToggle" title="Expandir/recolher menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-user">
        <span class="fw-bold d-none d-sm-inline">Olá, {{ session('usuario_nome') }}</span>
        <div class="avatar-circle" title="{{ session('usuario_nome') }}">
            {{ strtoupper(substr(session('usuario_nome', 'U'), 0, 1)) }}
        </div>
    </div>
</div>
