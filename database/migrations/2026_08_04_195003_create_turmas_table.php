<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete();
            $table->string('nome');
            $table->json('dias_semana');
            $table->unsignedTinyInteger('aulas_manha')->default(0);
            $table->time('inicio_manha')->nullable();
            $table->unsignedTinyInteger('aulas_tarde')->default(0);
            $table->time('inicio_tarde')->nullable();
            $table->unsignedSmallInteger('duracao_minutos')->default(50);
            $table->timestamps();

            $table->unique(['serie_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
