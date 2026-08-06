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
        
        $email = $request->input('email');
        $senha = $request->input('password');

        
        $usuario = User::where('email', $email)->first();

        
        if ($usuario && Hash::check($senha, $usuario->password)) {

            
            session(['usuario_id' => $usuario->id]);
            session(['usuario_nome' => $usuario->name]);
            session(['usuario_papel' => $usuario->papel]);

            
            return redirect('/modulos');
        }

        
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