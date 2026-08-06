<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-clipboard-check"></i>
        <span>Sale Marketing</span>
    </div>

    <ul class="sidebar-menu">
        @if(request()->is('grade*'))
            {{-- Módulo Grade de Horários --}}
            <span class="sidebar-section-label">Grade de Horários</span>
            <li>
                <a href="/grade" class="{{ request()->is('grade') ? 'active' : '' }}">
                    <i class="bi bi-house"></i>
                    <span class="link-text">Início</span>
                </a>
            </li>
            <li>
                <a href="/grade/materias" class="{{ request()->is('grade/materias*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i>
                    <span class="link-text">Matérias</span>
                </a>
            </li>
            <li>
                <a href="/grade/turmas" class="{{ request()->is('grade/turmas*') ? 'active' : '' }}">
                    <i class="bi bi-easel"></i>
                    <span class="link-text">Séries/Turmas</span>
                </a>
            </li>
            <li>
                <a href="/grade/professores" class="{{ request()->is('grade/professores*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i>
                    <span class="link-text">Professores</span>
                </a>
            </li>
            <li>
                <a href="/grade/gerar" class="{{ request()->is('grade/gerar') ? 'active' : '' }}">
                    <i class="bi bi-magic"></i>
                    <span class="link-text">Gerar Grade</span>
                </a>
            </li>
            <li>
                <a href="/grade/horarios" class="{{ request()->is('grade/horarios') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    <span class="link-text">Editar Horários</span>
                </a>
            </li>

            @if(session('usuario_papel') === 'master')
                <li>
                    <a href="/grade/escola/selecionar" class="{{ request()->is('grade/escola/selecionar') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span class="link-text">Trocar Escola</span>
                    </a>
                </li>
            @endif
        @else
            {{-- Módulo Tarefas --}}
            <span class="sidebar-section-label">Tarefas</span>
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
        @endif

        @if(session('usuario_papel') === 'master')
            <span class="sidebar-section-label">Administração</span>
            <li>
                <a href="/usuarios/listar" class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span class="link-text">Usuários</span>
                </a>
            </li>
            <li>
                <a href="/modulos" class="{{ request()->is('modulos') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span class="link-text">Selecionar Módulo</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        <a href="/auth/logout" class="d-flex align-items-center gap-3 text-decoration-none">
            <i class="bi bi-box-arrow-right"></i>
            <span class="link-text">Sair</span>
        </a>
    </div>
</div>
