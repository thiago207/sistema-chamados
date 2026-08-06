<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EscolaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (session()->has('escola_ativa_id')) {
            $builder->where($model->getTable().'.escola_id', session('escola_ativa_id'));
        }
    }
}
