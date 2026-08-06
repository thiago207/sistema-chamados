<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Horario;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Vinculo;
use App\Services\GeradorDeGradeService;
use App\Services\MontadorDeGradeService;
use App\Services\ValidadorDeViabilidadeService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $escola = Escola::find(session('escola_ativa_id'));

        return view('grade.index', ['escola' => $escola]);
    }

    public function gerar(ValidadorDeViabilidadeService $validador)
    {
        $problemas = $validador->validar();
        $cobertura = $validador->verificarCobertura();

        return view('grade.gerar', ['problemas' => $problemas, 'cobertura' => $cobertura]);
    }

    public function processar(ValidadorDeViabilidadeService $validador, GeradorDeGradeService $gerador)
    {
        $problemas = $validador->validar();

        if (! empty($problemas)) {
            return redirect('/grade/gerar')->with('erro', 'A grade não pode ser gerada enquanto houver problemas de cadastro.');
        }

        $resultado = $gerador->gerar();

        $mensagem = "Grade gerada: {$resultado['alocadas']} de {$resultado['total']} aula(s) alocada(s).";

        return redirect('/grade/horarios')
            ->with('sucesso', $mensagem)
            ->with('naoAlocadas', $resultado['naoAlocadas']);
    }

    public function horarios(Request $request)
    {
        $turmas = Turma::orderBy('nome')->get();
        $turmaId = $request->input('turma_id', optional($turmas->first())->id);
        $turma = $turmaId ? Turma::with('materias')->find($turmaId) : null;

        $grade = [];
        $opcoesPorSlot = [];

        if ($turma) {
            $horarios = Horario::with(['materia', 'professor'])->where('turma_id', $turma->id)->get();

            foreach ($horarios as $horario) {
                $grade[$horario->turno][$horario->numero_aula][$horario->dia_semana] = $horario;
            }

            $contagemAtual = $horarios->groupBy('materia_id')->map->count();

            $vinculos = Vinculo::where('turma_id', $turma->id)->get()->keyBy('materia_id');

            foreach ($turma->dias_semana as $dia) {
                foreach (['manha' => $turma->aulas_manha, 'tarde' => $turma->aulas_tarde] as $turno => $maximo) {
                    for ($numero = 1; $numero <= $maximo; $numero++) {
                        $chave = "{$dia}_{$turno}_{$numero}";
                        $ocupanteAtual = $grade[$turno][$numero][$dia] ?? null;
                        $opcoes = [];

                        foreach ($turma->materias as $materia) {
                            $vinculo = $vinculos->get($materia->id);

                            if (! $vinculo) {
                                continue;
                            }

                            $ehAtual = $ocupanteAtual && $ocupanteAtual->materia_id === $materia->id;
                            $usadas = $contagemAtual[$materia->id] ?? 0;

                            if (! $ehAtual && $usadas >= $materia->pivot->quantidade_aulas) {
                                continue; // matéria já usou toda a carga horária dela
                            }

                            $professorOcupado = Horario::where('professor_id', $vinculo->professor_id)
                                ->where('dia_semana', $dia)
                                ->where('turno', $turno)
                                ->where('numero_aula', $numero)
                                ->when($ocupanteAtual, fn ($q) => $q->where('id', '!=', $ocupanteAtual->id))
                                ->exists();

                            if ($professorOcupado) {
                                continue; // professor já está em outra turma nesse horário
                            }

                            $opcoes[] = [
                                'materia_id' => $materia->id,
                                'materia_nome' => $materia->nome,
                                'professor_id' => $vinculo->professor_id,
                                'professor_nome' => $vinculo->professor->nome ?? '',
                            ];
                        }

                        $opcoesPorSlot[$chave] = $opcoes;
                    }
                }
            }
        }

        return view('grade.horarios', [
            'turmas' => $turmas,
            'turma' => $turma,
            'grade' => $grade,
            'opcoesPorSlot' => $opcoesPorSlot,
        ]);
    }

    public function horariosGeral(ValidadorDeViabilidadeService $validador, MontadorDeGradeService $montador)
    {
        $turmas = Turma::with('materias')->orderBy('nome')->get();

        $problemasPorTurma = collect($validador->verificarCobertura())
            ->filter(fn ($problema) => isset($problema['turma_id']))
            ->groupBy('turma_id');

        $grades = $turmas->mapWithKeys(fn (Turma $turma) => [
            $turma->id => $montador->matrizDaTurma($turma),
        ]);

        return view('grade.horarios-geral', [
            'turmas' => $turmas,
            'grades' => $grades,
            'problemasPorTurma' => $problemasPorTurma,
        ]);
    }

    public function atualizarCelula(Request $request)
    {
        $dados = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'dia_semana' => 'required|integer|between:1,6',
            'turno' => 'required|in:manha,tarde',
            'numero_aula' => 'required|integer|min:1',
            'materia_id' => 'nullable|exists:materias,id',
        ]);

        Horario::where('turma_id', $dados['turma_id'])
            ->where('dia_semana', $dados['dia_semana'])
            ->where('turno', $dados['turno'])
            ->where('numero_aula', $dados['numero_aula'])
            ->delete();

        if (! empty($dados['materia_id'])) {
            $vinculo = Vinculo::where('turma_id', $dados['turma_id'])
                ->where('materia_id', $dados['materia_id'])
                ->first();

            if (! $vinculo) {
                return back()->withErrors(['materia_id' => 'Essa matéria não tem professor vinculado nessa turma.']);
            }

            $conflito = Horario::where('professor_id', $vinculo->professor_id)
                ->where('dia_semana', $dados['dia_semana'])
                ->where('turno', $dados['turno'])
                ->where('numero_aula', $dados['numero_aula'])
                ->exists();

            if ($conflito) {
                return back()->withErrors(['materia_id' => 'O professor dessa matéria já está em outra turma nesse horário.']);
            }

            Horario::create([
                'escola_id' => session('escola_ativa_id'),
                'turma_id' => $dados['turma_id'],
                'materia_id' => $dados['materia_id'],
                'professor_id' => $vinculo->professor_id,
                'dia_semana' => $dados['dia_semana'],
                'turno' => $dados['turno'],
                'numero_aula' => $dados['numero_aula'],
            ]);
        }

        return redirect("/grade/horarios?turma_id={$dados['turma_id']}")->with('sucesso', 'Horário atualizado com sucesso!');
    }

    public function horariosPorProfessor(Professor $professor)
    {
        $horarios = Horario::with(['turma', 'materia'])
            ->where('professor_id', $professor->id)
            ->get();

        $grade = [];
        foreach ($horarios as $horario) {
            $grade[$horario->turno][$horario->numero_aula][$horario->dia_semana] = $horario;
        }

        $maxManha = $professor->vinculos()->with('turma')->get()->max(fn ($v) => $v->turma->aulas_manha) ?: 0;
        $maxTarde = $professor->vinculos()->with('turma')->get()->max(fn ($v) => $v->turma->aulas_tarde) ?: 0;

        return view('grade.horarios-professor', [
            'professor' => $professor,
            'grade' => $grade,
            'maxManha' => $maxManha,
            'maxTarde' => $maxTarde,
        ]);
    }
}
