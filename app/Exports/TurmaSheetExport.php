<?php

namespace App\Exports;

use App\Models\Escola;
use App\Models\Turma;
use App\Services\MontadorDeGradeService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TurmaSheetExport implements FromView, WithTitle
{
    public function __construct(private Turma $turma, private MontadorDeGradeService $montador)
    {
    }

    public function view(): View
    {
        return view('grade.pdf.turma', [
            'turma' => $this->turma,
            'grade' => $this->montador->matrizDaTurma($this->turma),
            'escola' => Escola::find(session('escola_ativa_id')),
        ]);
    }

    public function title(): string
    {
        // Nome da aba do Excel: máximo 31 caracteres, sem : \ / ? * [ ]
        $titulo = $this->turma->nome;
        $titulo = preg_replace('/[:\\\\\/\?\*\[\]]/', '-', $titulo);

        return substr($titulo, 0, 31);
    }
}
