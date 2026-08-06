<?php

namespace App\Models;

use App\Models\Concerns\PertenceAEscola;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use PertenceAEscola;

    protected $fillable = [
        'escola_id',
        'turma_id',
        'materia_id',
        'professor_id',
        'dia_semana',
        'turno',
        'numero_aula',
        'editado_manualmente',
    ];

    protected $casts = [
        'editado_manualmente' => 'boolean',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
