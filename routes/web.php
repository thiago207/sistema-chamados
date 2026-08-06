<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisponibilidadeController;
use App\Http\Controllers\ExportacaoController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VinculoController;
use App\Http\Controllers\TarefaController;


Route::get('/', [AuthController::class, 'index']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::middleware('autenticado')->group(function () {

    Route::get('/modulos', [ModuloController::class, 'index']);

    Route::middleware('papel:master')->group(function () {
        Route::get('/usuarios/criar', [UsuarioController::class, 'index']);
        Route::post('/usuarios/cadastrar', [UsuarioController::class, 'criarUsuario']);
        Route::get('/usuarios/listar', [UsuarioController::class, 'listarUsuarios']);
        Route::delete('/usuarios/{id}/excluir', [UsuarioController::class, 'excluir']);
        Route::put('/usuarios/{id}/atualizar', [UsuarioController::class, 'atualizar']);
    });

    Route::middleware('papel:tarefas')->group(function () {
        Route::get('/menu', [MenuController::class, 'index']);

        Route::prefix('tarefas')->group(function () {
            Route::get('/', [TarefaController::class, 'listarTarefas']);
            Route::get('/criar', [TarefaController::class, 'index']);
            Route::get('/eventos', [TarefaController::class, 'eventos']);
            Route::post('/salvar', [TarefaController::class, 'salvarTarefa']);

            Route::get('/{id}', [TarefaController::class, 'show']);
            Route::put('/{id}/concluir', [TarefaController::class, 'concluir']);
            Route::put('/{id}/cancelar', [TarefaController::class, 'cancelar']);
        });
    });

    Route::prefix('grade')->middleware('papel:grade')->group(function () {
        Route::get('/escola/selecionar', [ModuloController::class, 'selecionarEscola'])->name('grade.escola.selecionar');
        Route::post('/escola/selecionar', [ModuloController::class, 'definirEscolaAtiva']);

        Route::middleware('escola.ativa')->group(function () {
            Route::get('/', [GradeController::class, 'index']);

            Route::prefix('materias')->group(function () {
                Route::get('/', [MateriaController::class, 'index']);
                Route::post('/', [MateriaController::class, 'store']);
                Route::put('/{materia}', [MateriaController::class, 'update']);
                Route::delete('/{materia}', [MateriaController::class, 'destroy']);
            });

            Route::prefix('turmas')->group(function () {
                Route::get('/', [TurmaController::class, 'index']);
                Route::get('/criar', [TurmaController::class, 'criar']);
                Route::post('/', [TurmaController::class, 'store']);
                Route::get('/{turma}/editar', [TurmaController::class, 'editar']);
                Route::put('/{turma}', [TurmaController::class, 'update']);
                Route::delete('/{turma}', [TurmaController::class, 'destroy']);
                Route::post('/{turma}/duplicar', [TurmaController::class, 'duplicar']);
                Route::get('/{turma}/materias-json', [VinculoController::class, 'materiasDaTurma']);
                Route::get('/{turma}', [TurmaController::class, 'show']);
            });

            Route::prefix('professores')->group(function () {
                Route::get('/', [ProfessorController::class, 'index']);
                Route::get('/criar', [ProfessorController::class, 'criar']);
                Route::post('/', [ProfessorController::class, 'store']);
                Route::get('/{professor}/editar', [ProfessorController::class, 'editar']);
                Route::put('/{professor}', [ProfessorController::class, 'update']);
                Route::delete('/{professor}', [ProfessorController::class, 'destroy']);

                Route::get('/{professor}/vinculos', [VinculoController::class, 'index']);
                Route::post('/{professor}/vinculos', [VinculoController::class, 'store']);

                Route::get('/{professor}/disponibilidade', [DisponibilidadeController::class, 'index']);
                Route::put('/{professor}/disponibilidade', [DisponibilidadeController::class, 'atualizar']);
            });

            Route::delete('/vinculos/{vinculo}', [VinculoController::class, 'destroy']);

            Route::get('/gerar', [GradeController::class, 'gerar']);
            Route::post('/gerar', [GradeController::class, 'processar']);

            Route::get('/horarios', [GradeController::class, 'horarios']);
            Route::get('/horarios/geral', [GradeController::class, 'horariosGeral']);
            Route::put('/horarios/celula', [GradeController::class, 'atualizarCelula']);
            Route::get('/horarios/professor/{professor}', [GradeController::class, 'horariosPorProfessor']);

            Route::prefix('exportar')->group(function () {
                Route::get('/pdf/turma/{turma}', [ExportacaoController::class, 'pdfTurma']);
                Route::get('/pdf/geral', [ExportacaoController::class, 'pdfGeral']);
                Route::get('/pdf/professor/{professor}', [ExportacaoController::class, 'pdfProfessor']);
                Route::get('/excel', [ExportacaoController::class, 'excelGeral']);
            });
        });
    });
});
