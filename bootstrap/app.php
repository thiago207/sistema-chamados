<?php

use App\Http\Middleware\Autenticado;
use App\Http\Middleware\EscolaAtiva;
use App\Http\Middleware\VerificaPapel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'autenticado' => Autenticado::class,
            'papel' => VerificaPapel::class,
            'escola.ativa' => EscolaAtiva::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
