<?php

namespace App\Http\Controllers;

use App\Exports\GradeExcelExport;
use App\Models\Escola;
use App\Models\Professor;
use App\Models\Turma;
use App\Services\MontadorDeGradeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportacaoController extends Controller
{
    public function __construct(private MontadorDeGradeService $montador)
    {
    }

    public function pdfTurma(Turma $turma)
    {
        $escola = Escola::find(session('escola_ativa_id'));

        $pdf = Pdf::loadView('grade.pdf.turma', [
            'turma' => $turma,
            'grade' => $this->montador->matrizDaTurma($turma),
            'escola' => $escola,
        ]);

        return $pdf->download("grade-{$turma->nome}.pdf");
    }

    public function pdfGeral()
    {
        $turmas = Turma::orderBy('nome')->get();
        $escola = Escola::find(session('escola_ativa_id'));

        $pdf = Pdf::loadView('grade.pdf.geral', [
            'turmas' => $turmas,
            'escola' => $escola,
            'montador' => $this->montador,
        ]);

        return $pdf->download('grade-geral.pdf');
    }

    public function pdfProfessor(Professor $professor)
    {
        $escola = Escola::find(session('escola_ativa_id'));

        $maxManha = $professor->vinculos()->with('turma')->get()->max(fn ($v) => $v->turma->aulas_manha) ?: 0;
        $maxTarde = $professor->vinculos()->with('turma')->get()->max(fn ($v) => $v->turma->aulas_tarde) ?: 0;

        $pdf = Pdf::loadView('grade.pdf.professor', [
            'professor' => $professor,
            'grade' => $this->montador->matrizPorProfessor($professor),
            'escola' => $escola,
            'maxManha' => $maxManha,
            'maxTarde' => $maxTarde,
        ]);

        return $pdf->download("grade-{$professor->nome}.pdf");
    }

    public function excelGeral()
    {
        if (! Turma::exists()) {
            return back()->withErrors(['exportar' => 'Nenhuma turma cadastrada ainda — não há o que exportar.']);
        }

        return Excel::download(new GradeExcelExport($this->montador), 'grade-geral.xlsx');
    }
}
