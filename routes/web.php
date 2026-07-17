<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TarefaController;


Route::get('/', [AuthController::class, 'index']);
Route::get('/menu', [MenuController::class, 'index']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::prefix('usuarios')->group(function () {
    Route::get('/criar', [UsuarioController::class, 'index']);
    Route::post('/cadastrar', [UsuarioController::class, 'criarUsuario']);   
    Route::get('/listar', [UsuarioController::class, 'listarUsuarios']); 
    Route::delete('/{id}/excluir', [UsuarioController::class, 'excluir']);
    Route::put('/{id}/atualizar', [UsuarioController::class, 'atualizar']);
});
Route::prefix('tarefas')->group(function () {
    Route::get('/', [TarefaController::class, 'listarTarefas']);
    Route::get('/criar', [TarefaController::class, 'index']);
    Route::post('/salvar', [TarefaController::class, 'salvarTarefa']);

    Route::put('/{id}/iniciar', [TarefaController::class, 'iniciar']);
    Route::put('/{id}/concluir', [TarefaController::class, 'concluir']);
    Route::put('/{id}/pausar', [TarefaController::class, 'pausar']);
    Route::put('/{id}/cancelar', [TarefaController::class, 'cancelar']);
});
    