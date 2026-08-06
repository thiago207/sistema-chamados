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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('escola_id')->nullable()->after('id')->constrained('escolas')->nullOnDelete();
            $table->enum('papel', ['master', 'tarefas', 'grade'])->default('tarefas')->after('escola_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['escola_id']);
            $table->dropColumn(['escola_id', 'papel']);
        });
    }
};
