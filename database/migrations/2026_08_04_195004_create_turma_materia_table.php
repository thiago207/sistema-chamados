<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turma_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->unsignedTinyInteger('quantidade_aulas');
            $table->timestamps();

            $table->unique(['turma_id', 'materia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turma_materia');
    }
};
