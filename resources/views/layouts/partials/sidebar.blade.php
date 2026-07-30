<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-clipboard-check"></i>
        <span>Sale Marketing</span>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="/menu" class="{{ request()->is('menu') ? 'active' : '' }}">
                <i class="bi bi-app"></i>
                <span class="link-text">Menu</span>
            </a>
        </li>

        <li>
            <a href="/tarefas" class="{{ request()->is('tarefas*') ? 'active' : '' }}">
                <i class="bi bi-list-task"></i>
                <span class="link-text">Tarefas</span>
            </a>
        </li>

        <li>
            <a href="/usuarios/listar" class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                <i class="bi bi-person-plus"></i>
                <span class="link-text">Usuários</span>
            </a>
        </li>

        {{-- Sair --}}
        <li>
            <a href="/auth/logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="link-text">Sair</span>
            </a>
        </li>
    </ul>
</div>
