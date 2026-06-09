<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Esercizi senza tag paradigma → tradizionale
        DB::table('esercizi')
            ->whereNull('paradigm_primary')
            ->orWhere('paradigm_primary', 'neutral')
            ->update(['paradigm_primary' => 'traditional']);
    }

    public function down(): void
    {
        // Non reversibile: non sappiamo quali erano null vs neutral prima
    }
};
