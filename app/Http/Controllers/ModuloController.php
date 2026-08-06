<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function index()
    {
        $usuario = User::find(session('usuario_id'));

        if ($usuario->isTarefas()) {
            return redirect('/tarefas');
        }

        if ($usuario->isGrade()) {
            return redirect('/grade');
        }

        return view('modulos.index');
    }

    public function selecionarEscola()
    {
        $escolas = Escola::orderBy('nome')->get();

        return view('modulos.selecionar-escola', ['escolas' => $escolas]);
    }

    public function definirEscolaAtiva(Request $request)
    {
        $request->validate([
            'escola_id' => 'required|exists:escolas,id',
        ]);

        session(['escola_ativa_id' => $request->input('escola_id')]);

        return redirect('/grade');
    }
}
