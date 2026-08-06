<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EscolaAtiva
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = User::find(session('usuario_id'));

        if (! $usuario->isMaster()) {
            session(['escola_ativa_id' => $usuario->escola_id]);

            return $next($request);
        }

        if (! session('escola_ativa_id') && ! $request->routeIs('grade.escola.selecionar')) {
            return redirect('/grade/escola/selecionar');
        }

        return $next($request);
    }
}
