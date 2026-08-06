<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidade;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisponibilidadeController extends Controller
{
    public function index(Professor $professor)
    {
        $professor->load(['vinculos.turma.materias', 'disponibilidades']);

        $maxManha = $professor->vinculos->max(fn ($v) => $v->turma->aulas_manha) ?: 6;
        $maxTarde = $professor->vinculos->max(fn ($v) => $v->turma->aulas_tarde) ?: 6;

        $aulasNecessarias = $professor->vinculos->sum(function ($vinculo) {
            $materiaNaTurma = $vinculo->turma->materias->firstWhere('id', $vinculo->materia_id);

            return $materiaNaTurma?->pivot->quantidade_aulas ?? 0;
        });

        $slotsMarcados = $professor->disponibilidades
            ->map(fn ($d) => "{$d->dia_semana}_{$d->turno}_{$d->numero_aula}")
            ->all();

        return view('grade.professores.disponibilidade', [
            'professor' => $professor,
            'maxManha' => $maxManha,
            'maxTarde' => $maxTarde,
            'aulasNecessarias' => $aulasNecessarias,
            'slotsMarcados' => $slotsMarcados,
        ]);
    }

    public function atualizar(Request $request, Professor $professor)
    {
        $dados = $request->validate([
            'slots' => 'nullable|array',
            'slots.*' => 'string',
        ]);

        $linhas = collect($dados['slots'] ?? [])->map(function ($slot) use ($professor) {
            [$dia, $turno, $numero] = explode('_', $slot);

            return [
                'professor_id' => $professor->id,
                'dia_semana' => (int) $dia,
                'turno' => $turno,
                'numero_aula' => (int) $numero,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::transaction(function () use ($professor, $linhas) {
            Disponibilidade::where('professor_id', $professor->id)->delete();

            if ($linhas->isNotEmpty()) {
                Disponibilidade::insert($linhas->all());
            }
        });

        return redirect("/grade/professores/{$professor->id}/disponibilidade")->with('sucesso', 'Disponibilidade atualizada com sucesso!');
    }
}
