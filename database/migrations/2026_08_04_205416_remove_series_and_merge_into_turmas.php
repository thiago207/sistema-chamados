<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropForeign(['serie_id']);
            $table->dropUnique('turmas_serie_id_nome_unique');
            $table->dropColumn('serie_id');
            $table->unique(['escola_id', 'nome']);
        });

        Schema::dropIfExists('series');
    }

    public function down(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->string('nome');
            $table->timestamps();

            $table->unique(['escola_id', 'nome']);
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->dropUnique(['escola_id', 'nome']);
            $table->foreignId('serie_id')->nullable()->after('escola_id')->constrained('series')->cascadeOnDelete();
        });
    }
};
