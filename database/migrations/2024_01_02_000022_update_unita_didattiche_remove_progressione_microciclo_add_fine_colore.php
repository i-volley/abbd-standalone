<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unita_didattiche', function (Blueprint $table) {
            if (Schema::hasColumn('unita_didattiche', 'microciclo_id')) {
                $table->dropForeign(['microciclo_id']);
                $table->dropColumn('microciclo_id');
            }
            if (Schema::hasColumn('unita_didattiche', 'progressione')) {
                $table->dropColumn('progressione');
            }
            if (!Schema::hasColumn('unita_didattiche', 'data_fine')) {
                $table->date('data_fine')->nullable()->after('data_inizio');
            }
            if (!Schema::hasColumn('unita_didattiche', 'colore')) {
                $table->string('colore', 7)->nullable()->default('#6366f1')->after('data_fine');
            }
        });
    }

    public function down(): void
    {
        Schema::table('unita_didattiche', function (Blueprint $table) {
            $table->dropColumn(['data_fine', 'colore']);
            $table->foreignId('microciclo_id')->nullable()->constrained('microcicli')->nullOnDelete();
            $table->enum('progressione', ['analitico_globale', 'sintetico_globale', 'libera'])
                ->default('analitico_globale');
        });
    }
};
