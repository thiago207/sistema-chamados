<?php

namespace App\Exports;

use App\Models\Turma;
use App\Services\MontadorDeGradeService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GradeExcelExport implements WithMultipleSheets
{
    public function __construct(private MontadorDeGradeService $montador)
    {
    }

    public function sheets(): array
    {
        return Turma::with('materias')
            ->orderBy('nome')
            ->get()
            ->map(fn (Turma $turma) => new TurmaSheetExport($turma, $this->montador))
            ->all();
    }
}
