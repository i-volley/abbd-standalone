<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            if (!Schema::hasColumn('sedute', 'unita_didattica_id')) {
                $table->foreignId('unita_didattica_id')
                      ->nullable()
                      ->after('microciclo_id')
                      ->constrained('unita_didattiche')
                      ->nullOnDelete();
            }
            if (!Schema::hasColumn('sedute', 'obiettivo_seduta')) {
                // Obiettivo principale variabile per questa seduta nell'unità
                $table->text('obiettivo_seduta')->nullable()->after('titolo');
            }
        });
        try {
            Schema::table('sedute', fn (Blueprint $t) => $t->index('unita_didattica_id'));
        } catch (\Exception $e) {
            // Indice già presente — ok
        }
    }

    public function down(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            $table->dropForeign(['unita_didattica_id']);
            $table->dropIndex(['unita_didattica_id']);
            $table->dropColumn(['unita_didattica_id', 'obiettivo_seduta']);
        });
    }
};
