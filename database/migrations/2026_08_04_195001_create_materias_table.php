<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('tipo', ['comum', 'eletiva', 'curricular'])->default('comum');
            $table->timestamps();

            $table->unique(['escola_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
