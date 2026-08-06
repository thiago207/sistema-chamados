<?php

namespace App\Models;

use App\Models\Concerns\PertenceAEscola;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use PertenceAEscola;

    protected $fillable = [
        'escola_id',
        'nome',
        'tipo',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'turma_materia')->withPivot('quantidade_aulas');
    }
}
