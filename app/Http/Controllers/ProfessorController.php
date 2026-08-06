<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $professores = Professor::when($busca, fn ($query) => $query->where('nome', 'like', "%{$busca}%"))
            ->withCount(['vinculos', 'disponibilidades'])
            ->orderBy('nome')
            ->get();

        return view('grade.professores.index', ['professores' => $professores, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
            'ativo' => 'nullable|boolean',
        ]);

        $dados['ativo'] = $request->boolean('ativo', true);

        Professor::create($dados);

        return redirect('/grade/professores')->with('sucesso', 'Professor cadastrado com sucesso!');
    }

    public function update(Request $request, Professor $professor)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        $dados['ativo'] = $request->boolean('ativo');

        $professor->update($dados);

        return redirect('/grade/professores')->with('sucesso', 'Professor atualizado com sucesso!');
    }

    public function destroy(Professor $professor)
    {
        $professor->delete();

        return redirect('/grade/professores')->with('sucesso', 'Professor excluído com sucesso!');
    }
}
