<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilidade extends Model
{
    protected $fillable = [
        'professor_id',
        'dia_semana',
        'turno',
        'numero_aula',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
