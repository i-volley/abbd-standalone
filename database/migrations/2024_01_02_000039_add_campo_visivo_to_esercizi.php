<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            if (!Schema::hasColumn('esercizi', 'campo_visivo')) {
                $table->json('campo_visivo')->nullable()->after('affordance_targets');
            }
        });
    }

    public function down(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            if (Schema::hasColumn('esercizi', 'campo_visivo')) {
                $table->dropColumn('campo_visivo');
            }
        });
    }
};
