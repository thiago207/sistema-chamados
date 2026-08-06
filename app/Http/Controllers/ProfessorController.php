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

    public function criar()
    {
        return view('grade.professores.criar');
    }

    public function store(Request $request)
    {
        $dados = $this->validarProfessor($request);

        $professor = Professor::create($dados);

        return redirect("/grade/professores/{$professor->id}/editar")
            ->with('sucesso', 'Professor cadastrado! Agora vincule as disciplinas/turmas e marque a disponibilidade dele.');
    }

    public function editar(Professor $professor)
    {
        $professor->loadCount(['vinculos', 'disponibilidades']);

        return view('grade.professores.editar', ['professor' => $professor]);
    }

    public function update(Request $request, Professor $professor)
    {
        $dados = $this->validarProfessor($request, $professor);
        $dados['ativo'] = $request->boolean('ativo');

        $professor->update($dados);

        return redirect('/grade/professores')->with('sucesso', 'Professor atualizado com sucesso!');
    }

    public function destroy(Professor $professor)
    {
        $professor->delete();

        return redirect('/grade/professores')->with('sucesso', 'Professor excluído com sucesso!');
    }

    private function validarProfessor(Request $request, ?Professor $professor = null): array
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        if (! $professor) {
            $dados['ativo'] = $request->boolean('ativo', true);
        }

        return $dados;
    }
}
