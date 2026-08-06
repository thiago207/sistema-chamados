<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Passo intermediário: adiciona o novo valor sem tirar os antigos,
        // pra poder migrar os dados existentes antes de travar o enum final.
        DB::statement("ALTER TABLE materias MODIFY tipo ENUM('comum', 'eletiva', 'curricular', 'comum_curricular') NOT NULL DEFAULT 'comum_curricular'");

        DB::table('materias')->whereIn('tipo', ['comum', 'curricular'])->update(['tipo' => 'comum_curricular']);

        DB::statement("ALTER TABLE materias MODIFY tipo ENUM('comum_curricular', 'eletiva') NOT NULL DEFAULT 'comum_curricular'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE materias MODIFY tipo ENUM('comum', 'eletiva', 'curricular', 'comum_curricular') NOT NULL DEFAULT 'comum'");

        DB::table('materias')->where('tipo', 'comum_curricular')->update(['tipo' => 'comum']);

        DB::statement("ALTER TABLE materias MODIFY tipo ENUM('comum', 'eletiva', 'curricular') NOT NULL DEFAULT 'comum'");
    }
};
