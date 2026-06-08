<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Soglie carico seduta configurabili per team
            // Salti: warning > 250, danger > 400 (default FIPAV load guidelines)
            $table->unsignedSmallInteger('soglia_salti_warn')->default(250)->after('stagione');
            $table->unsignedSmallInteger('soglia_salti_danger')->default(400)->after('soglia_salti_warn');
            $table->unsignedSmallInteger('soglia_gesti_warn')->default(400)->after('soglia_salti_danger');
            $table->unsignedSmallInteger('soglia_gesti_danger')->default(600)->after('soglia_gesti_warn');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['soglia_salti_warn', 'soglia_salti_danger', 'soglia_gesti_warn', 'soglia_gesti_danger']);
        });
    }
};
