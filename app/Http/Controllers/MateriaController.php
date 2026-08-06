<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $materias = Materia::when($busca, fn ($query) => $query->where('nome', 'like', "%{$busca}%"))
            ->orderBy('nome')
            ->get();

        return view('grade.materias.index', ['materias' => $materias, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materias')->where('escola_id', session('escola_ativa_id')),
            ],
            'tipo' => 'required|in:comum_curricular,eletiva',
        ]);

        Materia::create($dados);

        return redirect('/grade/materias')->with('sucesso', 'Matéria cadastrada com sucesso!');
    }

    public function update(Request $request, Materia $materia)
    {
        $dados = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materias')->where('escola_id', session('escola_ativa_id'))->ignore($materia->id),
            ],
            'tipo' => 'required|in:comum_curricular,eletiva',
        ]);

        $materia->update($dados);

        return redirect('/grade/materias')->with('sucesso', 'Matéria atualizada com sucesso!');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();

        return redirect('/grade/materias')->with('sucesso', 'Matéria excluída com sucesso!');
    }
}
