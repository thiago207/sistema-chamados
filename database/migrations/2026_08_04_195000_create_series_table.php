<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->string('nome');
            $table->timestamps();

            $table->unique(['escola_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
