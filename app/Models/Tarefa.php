<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    
    protected $fillable = [
    'titulo',
    'descricao',
    'criador_id',
    'status',
    'data',
    'observacoes',
    'concluida_em',
];
    protected $casts = [
    'data'         => 'date',
    'concluida_em' => 'datetime',
];
    public function responsaveis()
    {
        return $this->belongsToMany(User::class, 'tarefa_user');
    }
    public function criador()
    {
        return $this->belongsTo(User::class, 'criador_id');
    }
}
