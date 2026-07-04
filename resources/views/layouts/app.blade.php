<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Marketing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-clipboard-check"></i>
            <span>Sale Marketing</span>
        </div>

        <ul class="sidebar-menu">
            <!-- Grupo Tarefas -->
            <li>
                <a href="#submenuTarefas" data-bs-toggle="collapse" role="button" aria-expanded="false">
                    <i class="bi bi-list-task"></i>
                    <span class="link-text">Tarefas</span>
                    <i class="bi bi-chevron-down menu-toggle-chevron"></i>
                </a>
                <ul class="collapse submenu" id="submenuTarefas">
                    <li><a href="/tarefas/criar"><span class="link-text">Registrar tarefa</span></a></li>
                    <li><a href="/tarefas"><span class="link-text">Listar tarefa</span></a></li>
                </ul>
            </li>

            <!-- Grupo Cadastro -->
            <li>
                <a href="#submenuCadastro" data-bs-toggle="collapse" role="button" aria-expanded="false">
                    <i class="bi bi-person-plus"></i>
                    <span class="link-text">Cadastro</span>
                    <i class="bi bi-chevron-down menu-toggle-chevron"></i>
                </a>
                <ul class="collapse submenu" id="submenuCadastro">
                    <li><a href="/usuarios/criar"><span class="link-text">Cadastrar usuário</span></a></li>
                </ul>
                <ul class="collapse submenu" id="submenuCadastro">
                    <li><a href="/usuarios/listar"><span class="link-text">Listar usuários</span></a></li>
                </ul>
            </li>

            <!-- Sair -->
            <li>
                <a href="/auth/logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text">Sair</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- CONTEÚDO -->
    <div class="main-content" id="mainContent">
        <div class="topbar">
            <button class="btn-toggle" id="btnToggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="fw-bold" style="color: var(--azul-escuro);">
                Olá, {{ session('usuario_nome') }}
            </span>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>

        @yield('scripts')


</body>
</html>