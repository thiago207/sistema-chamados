<?php

namespace App\Models;

use App\Models\Concerns\PertenceAEscola;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use PertenceAEscola;

    protected $table = 'professores';

    protected $fillable = [
        'escola_id',
        'nome',
        'email',
        'telefone',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidade::class);
    }

    public function vinculos()
    {
        return $this->hasMany(Vinculo::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
