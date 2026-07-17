<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descricao');
            $table->text('observacoes')->nullable();

            $table->enum('status', [
                'pendente',
                'em_andamento',
                'concluida',
                'cancelada',
                'pausada'
            ])->default('pendente');

            $table->date('prazo')->nullable();

            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
