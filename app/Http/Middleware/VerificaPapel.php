<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaPapel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$papeis): Response
    {
        $usuario = User::find(session('usuario_id'));

        if (! $usuario || (! $usuario->isMaster() && ! in_array($usuario->papel, $papeis))) {
            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
