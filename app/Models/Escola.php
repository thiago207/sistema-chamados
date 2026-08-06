<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    protected $fillable = [
        'nome',
        'cidade',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    public function professores()
    {
        return $this->hasMany(Professor::class);
    }
}
