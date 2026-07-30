<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tarefas antigas sem prazo recebem a data de criação, já que a
        // coluna passa a ser obrigatória (data em que a tarefa deve ser feita).
        DB::table('tarefas')->whereNull('prazo')->update([
            'prazo' => DB::raw('DATE(created_at)'),
        ]);

        Schema::table('tarefas', function (Blueprint $table) {
            $table->renameColumn('prazo', 'data');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tarefas MODIFY data DATE NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tarefas MODIFY data DATE NULL');
        }

        Schema::table('tarefas', function (Blueprint $table) {
            $table->renameColumn('data', 'prazo');
        });
    }
};
