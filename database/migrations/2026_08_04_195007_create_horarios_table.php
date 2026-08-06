<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->enum('turno', ['manha', 'tarde']);
            $table->unsignedTinyInteger('numero_aula');
            $table->timestamps();

            $table->unique(['turma_id', 'dia_semana', 'turno', 'numero_aula']);
            $table->unique(['professor_id', 'dia_semana', 'turno', 'numero_aula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
