<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hash de senha


// Models
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('login.index'); 
    }
    

    public function login(Request $request)
    {
        // 1. Pega os dados do formulário
        $email = $request->input('email');
        $senha = $request->input('password');

        // 2. Busca o usuário no banco pelo email
        $usuario = User::where('email', $email)->first();

        // 3. Verifica se o usuário existe e se a senha bate
        if ($usuario && Hash::check($senha, $usuario->password)) {

            // 4. Cria a sessão com os dados do usuário
            session(['usuario_id' => $usuario->id]);
            session(['usuario_nome' => $usuario->name]);

            // 5. Redireciona pro dashboard
            return redirect('/menu');
        }

        // 6. Se falhar, volta pro login com mensagem de erro
        return view('login.index')->with('erro', 'Email ou senha inválidos');
    }
    public function logout()
    {
        // Limpa a sessão
        session()->flush();

        // Redireciona pro login
        return view('login.index')->with('sucesso', 'Você saiu com sucesso');
    }
}