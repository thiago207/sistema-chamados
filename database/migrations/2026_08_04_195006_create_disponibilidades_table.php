<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->enum('turno', ['manha', 'tarde']);
            $table->unsignedTinyInteger('numero_aula');
            $table->timestamps();

            $table->unique(['professor_id', 'dia_semana', 'turno', 'numero_aula'], 'disponibilidades_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidades');
    }
};
