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
     * Cada problema é ['mensagem' => string, 'acao_label' => ?string, 'acao_url' => ?string]
     * — o label/url apontam pra tela onde o problema se resolve.
     * Lista vazia significa que a grade pode ser gerada.
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
                        "{$turma->nome} possui apenas {$alocadas} aula(s) de {$materia->nome}, porém a matriz curricular exige {$exigidas} (faltam {$faltam}).",
                        'Ajustar horários manualmente',
                        "/grade/horarios?turma_id={$turma->id}"
                    ) + ['turma_id' => $turma->id];
                } elseif ($alocadas > $exigidas) {
                    $problemas[] = $this->problema(
                        "{$turma->nome} possui {$alocadas} aula(s) de {$materia->nome} alocada(s), porém a matriz curricular exige apenas {$exigidas} (excedente).",
                        'Ajustar horários manualmente',
                        "/grade/horarios?turma_id={$turma->id}"
                    ) + ['turma_id' => $turma->id];
                }
            }
        }

        return $problemas;
    }

    private function verificarVinculosFaltantesOuDuplicados(): array
    {
        $problemas = [];

        $turmas = Turma::with('materias')->get();

        foreach ($turmas as $turma) {
            foreach ($turma->materias as $materia) {
                $quantidadeVinculos = Vinculo::where('turma_id', $turma->id)
                    ->where('materia_id', $materia->id)
                    ->count();

                if ($quantidadeVinculos === 0) {
                    $problemas[] = $this->problema(
                        "{$turma->nome} — {$materia->nome}: nenhum professor vinculado.",
                        'Vincular um professor',
                        '/grade/professores'
                    );
                } elseif ($quantidadeVinculos > 1) {
                    $problemas[] = $this->problema(
                        "{$turma->nome} — {$materia->nome}: {$quantidadeVinculos} professores vinculados (só pode haver um).",
                        'Remover o vínculo duplicado',
                        '/grade/professores'
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
                    "{$turma->nome}: matriz curricular soma {$somaAulas} aula(s), mas a série/turma só tem {$maximo} slot(s) semanais ({$excedente} a mais).",
                    'Editar carga horária ou matriz curricular',
                    "/grade/turmas/{$turma->id}/editar"
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
                    "{$turma->nome}: nenhuma disciplina cadastrada na matriz curricular.",
                    'Cadastrar matriz curricular',
                    "/grade/turmas/{$turma->id}/editar"
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
                    "{$professor->nome}: precisa dar {$necessarias} aula(s) por semana, mas só marcou {$disponiveis} slot(s) disponível(is) (faltam {$faltam}).",
                    'Marcar mais disponibilidade',
                    "/grade/professores/{$professor->id}/disponibilidade"
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
                    "{$professor->nome}: tem vínculos de aula mas não marcou nenhuma disponibilidade.",
                    'Marcar disponibilidade',
                    "/grade/professores/{$professor->id}/disponibilidade"
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

    private function problema(string $mensagem, ?string $acaoLabel = null, ?string $acaoUrl = null): array
    {
        return [
            'mensagem' => $mensagem,
            'acao_label' => $acaoLabel,
            'acao_url' => $acaoUrl,
        ];
    }
}
