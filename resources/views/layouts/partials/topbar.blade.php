<div class="topbar">
    <button class="btn-toggle" id="btnToggle" title="Expandir/recolher menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-user dropdown">
        <button class="dropdown-toggle-user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="fw-semibold d-none d-sm-inline">{{ session('usuario_nome') }}</span>
            <div class="avatar-circle" title="{{ session('usuario_nome') }}">
                {{ strtoupper(substr(session('usuario_nome', 'U'), 0, 1)) }}
            </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2">
                <div class="fw-semibold">{{ session('usuario_nome') }}</div>
                @if(session('usuario_papel'))
                    <div class="text-muted small">{{ ucfirst(session('usuario_papel')) }}</div>
                @endif
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="/auth/logout">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </li>
        </ul>
    </div>
</div>
