<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Vinculo;

class ValidadorDeViabilidadeService
{
    /**
     * Roda todas as checagens PRÉ-geração e devolve uma lista de problemas.
     * Cada problema é:
     *   ['mensagem', 'tipo' ('erro'|'aviso'), 'acao_label', 'acao_url',
     *    'turma_id'?, 'turma_nome'?, 'professor_id'?, 'professor_nome'?]
     * — a mensagem já vem completa (turma/professor/disciplina incluídos,
     * sem exigir que a tela monte contexto); turma_id/professor_id servem
     * só pra agrupar/filtrar na tela. Lista vazia = grade pode ser gerada.
     */
    public function validar(): array
    {
        return [
            ...$this->verificarVinculosFaltantesOuDuplicados(),
            ...$this->verificarCargaExcedeSlots(),
            ...$this->verificarTurmaSemCarga(),
            ...$this->verificarDisponibilidadeSuficiente(),
            ...$this->verificarProfessorSemDisponibilidade(),
        ];
    }

    /**
     * Checagem PÓS-geração: compara o que foi efetivamente alocado na
     * grade com o que a matriz curricular de cada série/turma exige.
     */
    public function verificarCobertura(): array
    {
        $problemas = [];

        foreach (Turma::with('materias')->get() as $turma) {
            foreach ($turma->materias as $materia) {
                $exigidas = $materia->pivot->quantidade_aulas;
                $alocadas = Horario::where('turma_id', $turma->id)->where('materia_id', $materia->id)->count();

                if ($alocadas < $exigidas) {
                    $faltam = $exigidas - $alocadas;
                    $problemas[] = $this->problema(
                        "A turma {$turma->nome} tem apenas {$alocadas} aula(s) de {$materia->nome} na grade gerada, mas a matriz curricular exige {$exigidas} (faltam {$faltam}). Gere a grade novamente ou ajuste manualmente em Horários.",
                        'aviso',
                        'Ajustar horários manualmente',
                        "/grade/horarios?turma_id={$turma->id}",
                        turmaId: $turma->id,
                        turmaNome: $turma->nome,
                    );
                } elseif ($alocadas > $exigidas) {
                    $excedente = $alocadas - $exigidas;
                    $problemas[] = $this->problema(
                        "A turma {$turma->nome} tem {$alocadas} aula(s) de {$materia->nome} na grade gerada, mas a matriz curricular exige só {$exigidas} (excedente de {$excedente}). Ajuste manualmente em Horários.",
                        'aviso',
                        'Ajustar horários manualmente',
                        "/grade/horarios?turma_id={$turma->id}",
                        turmaId: $turma->id,
                        turmaNome: $turma->nome,
                    );
                }
            }
        }

        return $problemas;
    }

    /**
     * Problemas (pré-geração + cobertura) de UMA turma específica —
     * usado na tela de edição de horários, filtrado pelo que está
     * selecionado no momento. Inclui também problemas de professores que
     * têm vínculo com essa turma (ex: professor sem disponibilidade
     * suficiente), porque isso afeta diretamente a grade dessa turma
     * mesmo o problema sendo "do professor".
     */
    public function problemasDaTurma(int $turmaId): array
    {
        $professorIds = Vinculo::where('turma_id', $turmaId)->pluck('professor_id')->all();

        $todos = [...$this->validar(), ...$this->verificarCobertura()];

        return array_values(array_filter($todos, function ($p) use ($turmaId, $professorIds) {
            return $p['turma_id'] === $turmaId
                || ($p['professor_id'] !== null && in_array($p['professor_id'], $professorIds));
        }));
    }

    private function verificarVinculosFaltantesOuDuplicados(): array
    {
        $problemas = [];

        $turmas = Turma::with('materias')->get();

        foreach ($turmas as $turma) {
            foreach ($turma->materias as $materia) {
                $vinculos = Vinculo::with('professor')
                    ->where('turma_id', $turma->id)
                    ->where('materia_id', $materia->id)
                    ->get();

                if ($vinculos->isEmpty()) {
                    $problemas[] = $this->problema(
                        "A disciplina {$materia->nome} da turma {$turma->nome} ainda não tem professor vinculado — a grade não sabe quem alocar. Vá em Professores e crie o vínculo.",
                        'erro',
                        'Vincular um professor',
                        '/grade/professores',
                        turmaId: $turma->id,
                        turmaNome: $turma->nome,
                    );
                } elseif ($vinculos->count() > 1) {
                    $nomes = $vinculos->pluck('professor.nome')->filter()->implode(', ');
                    $problemas[] = $this->problema(
                        "A disciplina {$materia->nome} da turma {$turma->nome} está vinculada a {$vinculos->count()} professores ao mesmo tempo ({$nomes}) — só pode haver um. Remova o vínculo duplicado.",
                        'erro',
                        'Remover o vínculo duplicado',
                        '/grade/professores',
                        turmaId: $turma->id,
                        turmaNome: $turma->nome,
                    );
                }
            }
        }

        return $problemas;
    }

    private function verificarCargaExcedeSlots(): array
    {
        $problemas = [];

        foreach (Turma::with('materias')->get() as $turma) {
            $somaAulas = $turma->materias->sum(fn ($materia) => $materia->pivot->quantidade_aulas);
            $maximo = $turma->totalSlotsSemanais();

            if ($somaAulas > $maximo) {
                $excedente = $somaAulas - $maximo;
                $problemas[] = $this->problema(
                    "A turma {$turma->nome} tem {$somaAulas} aula(s) somadas na matriz curricular, mas o turno configurado só comporta {$maximo} aula(s) por semana ({$excedente} a mais). Reduza a carga de alguma disciplina ou aumente os horários da turma.",
                    'erro',
                    'Editar carga horária ou matriz curricular',
                    "/grade/turmas/{$turma->id}/editar",
                    turmaId: $turma->id,
                    turmaNome: $turma->nome,
                );
            }
        }

        return $problemas;
    }

    private function verificarTurmaSemCarga(): array
    {
        $problemas = [];

        foreach (Turma::with('materias')->get() as $turma) {
            if ($turma->materias->isEmpty()) {
                $problemas[] = $this->problema(
                    "A turma {$turma->nome} ainda não tem nenhuma disciplina cadastrada na matriz curricular.",
                    'erro',
                    'Cadastrar matriz curricular',
                    "/grade/turmas/{$turma->id}/editar",
                    turmaId: $turma->id,
                    turmaNome: $turma->nome,
                );
            }
        }

        return $problemas;
    }

    private function verificarDisponibilidadeSuficiente(): array
    {
        $problemas = [];

        foreach (Professor::with(['vinculos.turma.materias', 'disponibilidades'])->get() as $professor) {
            $necessarias = $this->aulasNecessarias($professor);
            $disponiveis = $professor->disponibilidades->count();

            if ($necessarias > $disponiveis) {
                $faltam = $necessarias - $disponiveis;
                $problemas[] = $this->problema(
                    "O professor {$professor->nome} precisa dar {$necessarias} aula(s) por semana (somando todas as turmas em que está vinculado), mas marcou apenas {$disponiveis} horário(s) disponível(is) — faltam {$faltam}. Marque mais disponibilidade pra ele.",
                    'erro',
                    'Marcar mais disponibilidade',
                    "/grade/professores/{$professor->id}/disponibilidade",
                    professorId: $professor->id,
                    professorNome: $professor->nome,
                );
            }
        }

        return $problemas;
    }

    private function verificarProfessorSemDisponibilidade(): array
    {
        $problemas = [];

        foreach (Professor::with(['vinculos', 'disponibilidades'])->get() as $professor) {
            if ($professor->vinculos->isNotEmpty() && $professor->disponibilidades->isEmpty()) {
                $problemas[] = $this->problema(
                    "O professor {$professor->nome} está vinculado a disciplinas mas ainda não marcou nenhum horário disponível — sem isso, a grade não consegue alocar as aulas dele.",
                    'erro',
                    'Marcar disponibilidade',
                    "/grade/professores/{$professor->id}/disponibilidade",
                    professorId: $professor->id,
                    professorNome: $professor->nome,
                );
            }
        }

        return $problemas;
    }

    private function aulasNecessarias(Professor $professor): int
    {
        return $professor->vinculos->sum(function ($vinculo) {
            $materiaNaTurma = $vinculo->turma->materias->firstWhere('id', $vinculo->materia_id);

            return $materiaNaTurma?->pivot->quantidade_aulas ?? 0;
        });
    }

    private function problema(
        string $mensagem,
        string $tipo,
        ?string $acaoLabel = null,
        ?string $acaoUrl = null,
        ?int $turmaId = null,
        ?string $turmaNome = null,
        ?int $professorId = null,
        ?string $professorNome = null,
    ): array {
        return [
            'mensagem' => $mensagem,
            'tipo' => $tipo,
            'acao_label' => $acaoLabel,
            'acao_url' => $acaoUrl,
            'turma_id' => $turmaId,
            'turma_nome' => $turmaNome,
            'professor_id' => $professorId,
            'professor_nome' => $professorNome,
        ];
    }
}
