<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\Professor;
use App\Models\Turma;

class MontadorDeGradeService
{
    /**
     * Monta a matriz turno => numero_aula => dia_semana => Horario da turma.
     * Fonte única usada tanto pela tela de visualização quanto pelos
     * exportadores de PDF e Excel, para os dois nunca divergirem.
     */
    public function matrizDaTurma(Turma $turma): array
    {
        $grade = [];

        foreach (Horario::with(['materia', 'professor'])->where('turma_id', $turma->id)->get() as $horario) {
            $grade[$horario->turno][$horario->numero_aula][$horario->dia_semana] = $horario;
        }

        return $grade;
    }

    public function matrizPorProfessor(Professor $professor): array
    {
        $grade = [];

        foreach (Horario::with(['turma', 'materia'])->where('professor_id', $professor->id)->get() as $horario) {
            $grade[$horario->turno][$horario->numero_aula][$horario->dia_semana] = $horario;
        }

        return $grade;
    }
}
