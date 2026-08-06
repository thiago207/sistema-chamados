<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\Vinculo;
use Illuminate\Support\Facades\DB;

class GeradorDeGradeService
{
    /** @var array<int, Turma> */
    private array $turmas = [];

    /** @var array<int, Professor> */
    private array $professores = [];

    /** Slots já ocupados por turma: [turma_id => [slot => true]] */
    private array $ocupacaoTurma = [];

    /** Slots já ocupados por professor: [professor_id => [slot => true]] */
    private array $ocupacaoProfessor = [];

    /** Slots já usados por cada par turma+matéria, para achar aulas geminadas */
    private array $slotsPorTurmaMateria = [];

    private int $backtracksUsados = 0;

    private const MAX_BACKTRACKS = 3000;

    /**
     * Gera a grade da escola ativa. Apaga os horários existentes e recria
     * tudo dentro de uma transação: se algo falhar no meio, a grade antiga
     * permanece intacta.
     *
     * @return array{alocadas: int, total: int, naoAlocadas: array<string>}
     */
    public function gerar(): array
    {
        return DB::transaction(function () {
            $escolaId = session('escola_ativa_id');

            Horario::where('escola_id', $escolaId)->delete();

            $this->carregarDados();

            $fila = $this->montarFilaDeAulas();
            $fila = $this->ordenarPorRestricao($fila);

            [$alocacoes, $naoAlocadas] = $this->alocarComBacktracking($fila);

            foreach ($alocacoes as $aula) {
                Horario::create([
                    'escola_id' => $escolaId,
                    'turma_id' => $aula['turma_id'],
                    'materia_id' => $aula['materia_id'],
                    'professor_id' => $aula['professor_id'],
                    'dia_semana' => $aula['dia_semana'],
                    'turno' => $aula['turno'],
                    'numero_aula' => $aula['numero_aula'],
                ]);
            }

            return [
                'alocadas' => count($alocacoes),
                'total' => count($fila),
                'naoAlocadas' => $naoAlocadas,
            ];
        });
    }

    private function carregarDados(): void
    {
        $this->turmas = Turma::with('materias')->get()->keyBy('id')->all();
        $this->professores = Professor::with('disponibilidades')->get()->keyBy('id')->all();
    }

    /**
     * Expande turma_materia em unidades individuais de aula.
     * Ex: 6A + Matemática + 5 aulas → 5 itens {turma, materia, professor}.
     */
    private function montarFilaDeAulas(): array
    {
        $fila = [];

        foreach ($this->turmas as $turma) {
            foreach ($turma->materias as $materia) {
                $vinculo = Vinculo::where('turma_id', $turma->id)
                    ->where('materia_id', $materia->id)
                    ->first();

                // Sem vínculo definido não dá pra alocar — o validador já
                // deveria ter barrado isso antes de chegar aqui.
                if (! $vinculo) {
                    continue;
                }

                for ($i = 0; $i < $materia->pivot->quantidade_aulas; $i++) {
                    $fila[] = [
                        'turma_id' => $turma->id,
                        'materia_id' => $materia->id,
                        'professor_id' => $vinculo->professor_id,
                    ];
                }
            }
        }

        return $fila;
    }

    /**
     * Heurística "Most Constrained First": aloca antes quem tem menos opção
     * de horário — professor com poucos slots disponíveis, matéria com mais
     * aulas semanais. Isso reduz muito a chance de travar no final.
     */
    private function ordenarPorRestricao(array $fila): array
    {
        $disponibilidadePorProfessor = [];
        foreach ($this->professores as $professor) {
            $disponibilidadePorProfessor[$professor->id] = count($professor->disponibilidades);
        }

        $quantidadePorTurmaMateria = [];
        foreach ($fila as $aula) {
            $chave = $aula['turma_id'].'_'.$aula['materia_id'];
            $quantidadePorTurmaMateria[$chave] = ($quantidadePorTurmaMateria[$chave] ?? 0) + 1;
        }

        usort($fila, function ($a, $b) use ($disponibilidadePorProfessor, $quantidadePorTurmaMateria) {
            $slotsA = $disponibilidadePorProfessor[$a['professor_id']] ?? 0;
            $slotsB = $disponibilidadePorProfessor[$b['professor_id']] ?? 0;

            if ($slotsA !== $slotsB) {
                return $slotsA <=> $slotsB; // menos slots primeiro
            }

            $chaveA = $a['turma_id'].'_'.$a['materia_id'];
            $chaveB = $b['turma_id'].'_'.$b['materia_id'];

            return $quantidadePorTurmaMateria[$chaveB] <=> $quantidadePorTurmaMateria[$chaveA]; // mais aulas primeiro
        });

        return array_values($fila);
    }

    /**
     * Percorre a fila tentando alocar cada aula. Quando uma aula não acha
     * slot, desfaz a alocação anterior e tenta de novo por outro caminho
     * (backtracking), até um limite de tentativas — depois disso, a aula
     * é registrada como não alocada e o algoritmo segue em frente, para
     * sempre devolver uma grade parcial em vez de travar.
     */
    private function alocarComBacktracking(array $fila): array
    {
        $indice = 0;
        $alocacoes = []; // indice => slot escolhido
        $excluidosPorIndice = []; // indice => [slots já tentados e descartados nessa posição]
        $naoAlocadas = [];
        $desistidas = []; // indice => true — aulas definitivamente sem solução, nunca mais tentadas

        while ($indice < count($fila)) {
            // Posição já desistida: não tem alocação pra desfazer nem faz
            // sentido tentar de novo, só passa pra próxima.
            if (isset($desistidas[$indice])) {
                $indice++;

                continue;
            }

            $aula = $fila[$indice];
            $slot = $this->buscarSlotValido($aula, $excluidosPorIndice[$indice] ?? []);

            if ($slot !== null) {
                $this->ocupar($aula, $slot);
                $alocacoes[$indice] = $aula + $this->decompoeSlot($slot);
                $indice++;

                continue;
            }

            // Não achou slot nessa posição — tenta desfazer a alocação
            // anterior e escolher outro caminho para ela. Posições já
            // desistidas não têm nada pra desfazer, então pula pra trás
            // até achar uma que tenha uma alocação de verdade.
            $anterior = $indice - 1;
            while ($anterior >= 0 && isset($desistidas[$anterior])) {
                $anterior--;
            }

            if ($anterior < 0 || $this->backtracksUsados >= self::MAX_BACKTRACKS) {
                $naoAlocadas[] = $this->descreverAulaNaoAlocada($aula);
                $desistidas[$indice] = true;
                unset($excluidosPorIndice[$indice]);
                $indice++;

                continue;
            }

            $this->backtracksUsados++;
            $indice = $anterior;

            $aulaAnterior = $fila[$indice];
            $slotAnterior = $this->compoeSlot($alocacoes[$indice]);
            $this->desocupar($aulaAnterior, $slotAnterior);

            $excluidosPorIndice[$indice][] = $slotAnterior;
            unset($alocacoes[$indice]);
        }

        return [array_values($alocacoes), $naoAlocadas];
    }

    private function buscarSlotValido(array $aula, array $excluidos): ?string
    {
        $turma = $this->turmas[$aula['turma_id']];
        $professor = $this->professores[$aula['professor_id']];

        $candidatos = collect($professor->disponibilidades)
            ->filter(function ($d) use ($turma) {
                $numeroValido = $d->turno === 'manha'
                    ? $d->numero_aula <= $turma->aulas_manha
                    : $d->numero_aula <= $turma->aulas_tarde;

                return in_array($d->dia_semana, $turma->dias_semana) && $numeroValido;
            })
            ->map(fn ($d) => "{$d->dia_semana}_{$d->turno}_{$d->numero_aula}")
            ->unique()
            ->reject(function ($slot) use ($turma, $professor, $excluidos) {
                return isset($this->ocupacaoTurma[$turma->id][$slot])
                    || isset($this->ocupacaoProfessor[$professor->id][$slot])
                    || in_array($slot, $excluidos);
            })
            ->sort()
            ->values();

        if ($candidatos->isEmpty()) {
            return null;
        }

        // Preferência por aula geminada: slot adjacente (mesmo dia+turno,
        // número vizinho) a outra aula já alocada da mesma turma+matéria.
        $chaveTurmaMateria = $aula['turma_id'].'_'.$aula['materia_id'];
        $slotsExistentes = $this->slotsPorTurmaMateria[$chaveTurmaMateria] ?? [];

        foreach ($candidatos as $slot) {
            [$dia, $turno, $numero] = explode('_', $slot);

            foreach ($slotsExistentes as $existente) {
                [$diaE, $turnoE, $numeroE] = explode('_', $existente);

                if ($dia === $diaE && $turno === $turnoE && abs((int) $numero - (int) $numeroE) === 1) {
                    return $slot;
                }
            }
        }

        return $candidatos->first();
    }

    private function ocupar(array $aula, string $slot): void
    {
        $this->ocupacaoTurma[$aula['turma_id']][$slot] = true;
        $this->ocupacaoProfessor[$aula['professor_id']][$slot] = true;

        $chave = $aula['turma_id'].'_'.$aula['materia_id'];
        $this->slotsPorTurmaMateria[$chave][] = $slot;
    }

    private function desocupar(array $aula, string $slot): void
    {
        unset($this->ocupacaoTurma[$aula['turma_id']][$slot]);
        unset($this->ocupacaoProfessor[$aula['professor_id']][$slot]);

        $chave = $aula['turma_id'].'_'.$aula['materia_id'];
        $this->slotsPorTurmaMateria[$chave] = array_values(
            array_diff($this->slotsPorTurmaMateria[$chave] ?? [], [$slot])
        );
    }

    private function decompoeSlot(string $slot): array
    {
        [$dia, $turno, $numero] = explode('_', $slot);

        return [
            'dia_semana' => (int) $dia,
            'turno' => $turno,
            'numero_aula' => (int) $numero,
        ];
    }

    private function compoeSlot(array $aula): string
    {
        return "{$aula['dia_semana']}_{$aula['turno']}_{$aula['numero_aula']}";
    }

    private function descreverAulaNaoAlocada(array $aula): string
    {
        $turma = $this->turmas[$aula['turma_id']];
        $materia = $turma->materias->firstWhere('id', $aula['materia_id']);
        $professor = $this->professores[$aula['professor_id']];

        return "{$turma->nome} — {$materia?->nome}: 1 aula não alocada (professor {$professor->nome} sem slots livres compatíveis).";
    }
}
