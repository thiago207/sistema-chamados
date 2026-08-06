<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hash de senha


// Models
use App\Models\Escola;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        $escolas = Escola::orderBy('nome')->get();

        return view('usuarios.cadastrar', ['escolas' => $escolas]);
    }
    public function criarUsuario(Request $request)
    {
        // 1. Valida os dados
        $dados = $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed',
            'papel'     => 'required|in:master,tarefas,grade',
            'escola_id' => 'required_if:papel,grade|nullable|exists:escolas,id',
        ]);

        // 2. Cria o usuário no banco
        User::create([
            'name'      => $dados['name'],
            'email'     => $dados['email'],
            'password'  => Hash::make($dados['password']),
            'papel'     => $dados['papel'],
            'escola_id' => $dados['papel'] === 'grade' ? $dados['escola_id'] : null,
        ]);

        // 3. Redireciona com sucesso
        return redirect('usuarios/criar')->with('sucesso', 'Usuário cadastrado com sucesso!');
    }
    public function listarUsuarios()
    {
        // 1. Busca todos os usuários
        $usuarios = User::with('escola')->get();
        $escolas = Escola::orderBy('nome')->get();

        // 2. Retorna a view com os usuários
        return view('usuarios.listar', ['usuarios' => $usuarios, 'escolas' => $escolas]);
    }
    public function excluir($id)
    {
        // 1. Busca o usuário pelo id
        $usuario = User::find($id);

        // 2. Deleta
        $usuario->delete();

        // 3. Redireciona com sucesso
        return redirect('/usuarios/listar')->with('sucesso', 'Usuário excluído com sucesso!');
    }
    
    public function atualizar(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,' . $id,
            'password'  => 'nullable|min:8|confirmed',
            'papel'     => 'required|in:master,tarefas,grade',
            'escola_id' => 'required_if:papel,grade|nullable|exists:escolas,id',
        ]);

        $usuario = User::find($id);

        $dados = [
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'papel'     => $request->input('papel'),
            'escola_id' => $request->input('papel') === 'grade' ? $request->input('escola_id') : null,
        ];

        // Só atualiza a senha se o campo foi preenchido
        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->input('password'));
        }

        $usuario->update($dados);

        return redirect('/usuarios/listar')->with('sucesso', 'Usuário atualizado com sucesso!');
    }

}