<?php

namespace App\Models;

use App\Models\Concerns\PertenceAEscola;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use PertenceAEscola;

    protected $fillable = [
        'escola_id',
        'nome',
        'dias_semana',
        'aulas_manha',
        'inicio_manha',
        'aulas_tarde',
        'inicio_tarde',
        'duracao_minutos',
    ];

    protected $casts = [
        'dias_semana' => 'array',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'turma_materia')->withPivot('quantidade_aulas');
    }

    public function vinculos()
    {
        return $this->hasMany(Vinculo::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function totalSlotsSemanais(): int
    {
        return count($this->dias_semana ?? []) * ($this->aulas_manha + $this->aulas_tarde);
    }

    public function horarioDoSlot(string $turno, int $numeroAula): ?string
    {
        $inicio = $turno === 'manha' ? $this->inicio_manha : $this->inicio_tarde;

        if (! $inicio) {
            return null;
        }

        $inicioAula = Carbon::parse($inicio)->addMinutes(($numeroAula - 1) * $this->duracao_minutos);
        $fimAula = $inicioAula->copy()->addMinutes($this->duracao_minutos);

        return $inicioAula->format('H:i').' às '.$fimAula->format('H:i');
    }
}
