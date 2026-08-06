<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Vinculo;
use Illuminate\Http\Request;

class VinculoController extends Controller
{
    public function index(Professor $professor)
    {
        $professor->load(['vinculos.turma', 'vinculos.materia']);
        $turmas = Turma::orderBy('nome')->get();

        return view('grade.professores.vinculos', ['professor' => $professor, 'turmas' => $turmas]);
    }

    public function materiasDaTurma(Turma $turma)
    {
        $turma->load('materias');

        return response()->json(
            $turma->materias->map(fn (Materia $materia) => [
                'id' => $materia->id,
                'nome' => $materia->nome,
            ])
        );
    }

    public function store(Request $request, Professor $professor)
    {
        $dados = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $jaVinculado = Vinculo::where('turma_id', $dados['turma_id'])
            ->where('materia_id', $dados['materia_id'])
            ->where('professor_id', '!=', $professor->id)
            ->with('professor')
            ->first();

        if ($jaVinculado) {
            return back()->withErrors([
                'materia_id' => "Essa matéria nessa turma já está vinculada a {$jaVinculado->professor->nome}.",
            ])->withInput();
        }

        $existe = Vinculo::where('professor_id', $professor->id)
            ->where('turma_id', $dados['turma_id'])
            ->where('materia_id', $dados['materia_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['materia_id' => 'Esse vínculo já existe.'])->withInput();
        }

        Vinculo::create([
            'professor_id' => $professor->id,
            'turma_id' => $dados['turma_id'],
            'materia_id' => $dados['materia_id'],
        ]);

        return redirect("/grade/professores/{$professor->id}/vinculos")->with('sucesso', 'Vínculo adicionado com sucesso!');
    }

    public function destroy(Vinculo $vinculo)
    {
        $professorId = $vinculo->professor_id;
        $vinculo->delete();

        return redirect("/grade/professores/{$professorId}/vinculos")->with('sucesso', 'Vínculo removido com sucesso!');
    }
}
