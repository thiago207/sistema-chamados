<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vinculo extends Model
{
    protected $table = 'professor_turma_materia';

    protected $fillable = [
        'professor_id',
        'turma_id',
        'materia_id',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}
