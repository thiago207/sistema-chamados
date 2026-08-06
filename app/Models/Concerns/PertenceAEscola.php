<?php

namespace App\Models\Concerns;

use App\Models\Scopes\EscolaScope;

trait PertenceAEscola
{
    protected static function bootPertenceAEscola(): void
    {
        static::addGlobalScope(new EscolaScope);

        static::creating(function ($model) {
            if (empty($model->escola_id) && session()->has('escola_ativa_id')) {
                $model->escola_id = session('escola_ativa_id');
            }
        });
    }
}
