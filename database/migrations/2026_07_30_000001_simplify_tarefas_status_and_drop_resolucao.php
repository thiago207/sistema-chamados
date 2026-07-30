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
        // Tarefas em andamento/pausadas viram pendentes — esses status deixam de existir.
        DB::table('tarefas')
            ->whereIn('status', ['em_andamento', 'pausada'])
            ->update(['status' => 'pendente']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tarefas MODIFY status ENUM('pendente','concluida','cancelada') NOT NULL DEFAULT 'pendente'");
        }

        Schema::table('tarefas', function (Blueprint $table) {
            $table->dropColumn('resolucao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarefas', function (Blueprint $table) {
            $table->text('resolucao')->nullable()->after('observacoes');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tarefas MODIFY status ENUM('pendente','em_andamento','concluida','cancelada','pausada') NOT NULL DEFAULT 'pendente'");
        }
    }
};
