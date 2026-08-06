@extends('layouts.app')

@section('titulo', 'Usuários · Sale Marketing')

@section('content')

@include('partials.breadcrumb', ['items' => [
    ['label' => 'Menu', 'url' => '/menu'],
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

@if($usuarios->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <strong>Nenhum usuário cadastrado</strong>
                Cadastre o primeiro usuário para começar.
            </div>
        </div>
    </div>
@else

    {{-- CARTÕES — celular --}}
    <div class="d-md-none d-flex flex-column gap-3">
        @foreach($usuarios as $usuario)
            <div class="card task-card">
                <div class="card-body">
                    <div class="fw-semibold text-brand mb-1">{{ $usuario->name }}</div>
                    <div class="text-muted small mb-1">{{ $usuario->email }}</div>
                    <div class="text-muted small mb-1">
                        {{ ucfirst($usuario->papel) }}
                        @if($usuario->escola)
                            · {{ $usuario->escola->nome }}
                        @endif
                    </div>
                    <div class="text-muted small mb-3">Cadastrado em {{ $usuario->created_at->format('d/m/Y') }}</div>

                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-primary flex-fill"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar{{ $usuario->id }}"
                            title="Editar">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        <form action="/usuarios/{{ $usuario->id }}/excluir" method="POST"
                            data-confirm="Tem certeza que deseja excluir?" data-confirm-tipo="danger" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" title="Excluir">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TABELA — telas médias e maiores --}}
    <div class="card d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Papel</th>
                            <th>Cadastrado em</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td class="ps-4 text-muted">{{ $usuario->id }}</td>
                                <td class="fw-semibold text-brand">{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    <small>
                                        {{ ucfirst($usuario->papel) }}
                                        @if($usuario->escola)
                                            · {{ $usuario->escola->nome }}
                                        @endif
                                    </small>
                                </td>
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
                                            data-confirm="Tem certeza que deseja excluir?" data-confirm-tipo="danger">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modais de edição — um por usuário, reaproveitado pelo cartão e pela tabela --}}
    @foreach($usuarios as $usuario)
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

                            <div class="form-floating mb-3">
                                <input type="text" id="nome{{ $usuario->id }}" name="name" class="form-control" value="{{ $usuario->name }}" placeholder="Nome" required>
                                <label for="nome{{ $usuario->id }}">Nome</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" id="email{{ $usuario->id }}" name="email" class="form-control" value="{{ $usuario->email }}" placeholder="Email" required>
                                <label for="email{{ $usuario->id }}">Email</label>
                            </div>

                            {{-- Senha com olhinho --}}
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input type="password" id="senha{{ $usuario->id }}" name="password"
                                        class="form-control campo-senha"
                                        placeholder="Deixe em branco para não alterar">
                                    <label for="senha{{ $usuario->id }}">Nova senha</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-olho">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            {{-- Confirmar senha — escondido por padrão --}}
                            <div class="form-floating mb-3 grupo-confirmar" style="display: none;">
                                <input type="password" id="confirmarSenha{{ $usuario->id }}" name="password_confirmation"
                                    class="form-control"
                                    placeholder="Repita a nova senha">
                                <label for="confirmarSenha{{ $usuario->id }}">Confirmar nova senha</label>
                            </div>

                            {{-- Papel --}}
                            <div class="form-floating mb-3">
                                <select id="papel{{ $usuario->id }}" name="papel" class="form-select campo-papel">
                                    <option value="tarefas" {{ $usuario->papel === 'tarefas' ? 'selected' : '' }}>Tarefas</option>
                                    <option value="grade" {{ $usuario->papel === 'grade' ? 'selected' : '' }}>Grade de Horários</option>
                                    <option value="master" {{ $usuario->papel === 'master' ? 'selected' : '' }}>Master (acesso total)</option>
                                </select>
                                <label for="papel{{ $usuario->id }}">Papel</label>
                            </div>

                            {{-- Escola — só faz sentido pro papel "grade" --}}
                            <div class="form-floating mb-3 grupo-escola" style="display: none;">
                                <select id="escola{{ $usuario->id }}" name="escola_id" class="form-select">
                                    <option value="" disabled {{ !$usuario->escola_id ? 'selected' : '' }}>Selecione...</option>
                                    @foreach($escolas as $escola)
                                        <option value="{{ $escola->id }}" {{ $usuario->escola_id == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                                    @endforeach
                                </select>
                                <label for="escola{{ $usuario->id }}">Escola</label>
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
    @endforeach
@endif

@endsection
@section('scripts')
    <script src="{{ asset('js/usuarios.js') }}"></script>
@endsection
