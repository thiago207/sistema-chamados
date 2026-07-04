@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Mensagem de sucesso --}}
        @if(session('sucesso'))
            <div class="alert alert-success">{{ session('sucesso') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center text-white fw-bold" style="background-color: #1a3a6b;">
                <span>Usuários Cadastrados</span>
                <a href="/usuarios/criar" class="btn btn-sm text-white" style="background-color: #c0392b;">
                    + Novo Usuário
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background-color: #f4f6f9;">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cadastrado em</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id }}</td>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar{{ $usuario->id }}">
                                            Editar
                                        </button>

                                        <form action="/usuarios/{{ $usuario->id }}/excluir" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal de edição --}}
                            <div class="modal fade" id="modalEditar{{ $usuario->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-white" style="background-color: #1a3a6b;">
                                            <h5 class="modal-title">Editar Usuário</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="/usuarios/{{ $usuario->id }}/atualizar" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold" style="color: #1a3a6b;">Nome</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $usuario->name }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold" style="color: #1a3a6b;">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required>
                                                </div>

                                                {{-- Senha com olhinho --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold" style="color: #1a3a6b;">Nova Senha</label>
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
                                                    <label class="form-label fw-bold" style="color: #1a3a6b;">Confirmar Nova Senha</label>
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control"
                                                        placeholder="Repita a nova senha">
                                                </div>

                                                <div class="modal-footer px-0 pb-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn text-white fw-bold" style="background-color: #c0392b;">Salvar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhum usuário cadastrado ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
    <script src="{{ asset('js/usuarios.js') }}"></script>
@endsection