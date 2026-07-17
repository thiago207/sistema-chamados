@extends('layouts.app')

@section('titulo', 'Usuários · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
    ['label' => 'Cadastro'],
    ['label' => 'Usuários'],
]])

<div class="page-header">
    <div>
        <h1 class="page-header__title">Usuários Cadastrados</h1>
        <p class="page-header__subtitle mb-0">{{ $usuarios->count() }} usuário(s) no total</p>
    </div>
    <a href="/usuarios/criar" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Novo Usuário
    </a>
</div>

{{-- Mensagem de sucesso --}}
@if(session('sucesso'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cadastrado em</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td class="ps-4 text-muted">{{ $usuario->id }}</td>
                            <td class="fw-semibold text-brand">{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td><small>{{ $usuario->created_at->format('d/m/Y') }}</small></td>
                            <td class="pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $usuario->id }}"
                                        title="Editar">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>

                                    <form action="/usuarios/{{ $usuario->id }}/excluir" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal de edição --}}
                        <div class="modal fade" id="modalEditar{{ $usuario->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-brand text-white">
                                        <h5 class="modal-title">Editar Usuário</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="/usuarios/{{ $usuario->id }}/atualizar" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label class="form-label">Nome</label>
                                                <input type="text" name="name" class="form-control" value="{{ $usuario->name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required>
                                            </div>

                                            {{-- Senha com olhinho --}}
                                            <div class="mb-3">
                                                <label class="form-label">Nova Senha</label>
                                                <div class="input-group">
                                                    <input type="password" name="password"
                                                        class="form-control campo-senha"
                                                        placeholder="Deixe em branco para não alterar">
                                                    <button type="button" class="btn btn-outline-secondary btn-olho">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Confirmar senha — escondido por padrão --}}
                                            <div class="mb-3 grupo-confirmar" style="display: none;">
                                                <label class="form-label">Confirmar Nova Senha</label>
                                                <input type="password" name="password_confirmation"
                                                    class="form-control"
                                                    placeholder="Repita a nova senha">
                                            </div>

                                            <div class="modal-footer px-0 pb-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-brand fw-bold">Salvar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <strong>Nenhum usuário cadastrado</strong>
                                    Cadastre o primeiro usuário para começar.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script src="{{ asset('js/usuarios.js') }}"></script>
@endsection
