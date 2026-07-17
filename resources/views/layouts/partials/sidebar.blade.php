@php
    $tarefasAtivo = request()->is('tarefas*');
    $cadastroAtivo = request()->is('usuarios*');
@endphp
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

        {{-- Grupo Tarefas --}}
        <li>
            <a href="#submenuTarefas" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ $tarefasAtivo ? 'true' : 'false' }}"
               class="{{ $tarefasAtivo ? 'active' : '' }}">
                <i class="bi bi-list-task"></i>
                <span class="link-text">Tarefas</span>
                <i class="bi bi-chevron-down menu-toggle-chevron"></i>
            </a>
            <ul class="collapse submenu {{ $tarefasAtivo ? 'show' : '' }}" id="submenuTarefas">
                <li>
                    <a href="/tarefas/criar" class="{{ request()->is('tarefas/criar') ? 'active' : '' }}">
                        <span class="link-text">Registrar tarefa</span>
                    </a>
                </li>
                <li>
                    <a href="/tarefas" class="{{ request()->is('tarefas') ? 'active' : '' }}">
                        <span class="link-text">Listar tarefa</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Grupo Cadastro --}}
        <li>
            <a href="#submenuCadastro" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ $cadastroAtivo ? 'true' : 'false' }}"
               class="{{ $cadastroAtivo ? 'active' : '' }}">
                <i class="bi bi-person-plus"></i>
                <span class="link-text">Cadastro</span>
                <i class="bi bi-chevron-down menu-toggle-chevron"></i>
            </a>
            <ul class="collapse submenu {{ $cadastroAtivo ? 'show' : '' }}" id="submenuCadastro">
                <li>
                    <a href="/usuarios/criar" class="{{ request()->is('usuarios/criar') ? 'active' : '' }}">
                        <span class="link-text">Cadastrar usuário</span>
                    </a>
                </li>
                <li>
                    <a href="/usuarios/listar" class="{{ request()->is('usuarios/listar') ? 'active' : '' }}">
                        <span class="link-text">Listar usuários</span>
                    </a>
                </li>
            </ul>
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
