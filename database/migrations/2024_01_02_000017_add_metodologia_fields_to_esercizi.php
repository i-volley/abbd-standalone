<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotente: MySQL DDL non è transazionale, possibile run parziale su Railway.
        // hasColumn evita "Duplicate column name" su re-run.
        Schema::table('esercizi', function (Blueprint $table) {
            if (!Schema::hasColumn('esercizi', 'obiettivo')) {
                $table->enum('obiettivo', ['permanente', 'principale', 'secondario'])->nullable()->after('metodologia');
            }
            if (!Schema::hasColumn('esercizi', 'fase_seduta')) {
                $table->enum('fase_seduta', ['preparatoria', 'centrale', 'finale'])->nullable()->after('obiettivo');
            }
            if (!Schema::hasColumn('esercizi', 'fase_gioco')) {
                $table->enum('fase_gioco', ['cambio_palla', 'break_point', 'ricostruzione'])->nullable()->after('fase_seduta');
            }
            if (!Schema::hasColumn('esercizi', 'componente')) {
                $table->enum('componente', ['tecnica', 'tattica'])->nullable()->after('fase_gioco');
            }
            if (!Schema::hasColumn('esercizi', 'rendimento')) {
                $table->enum('rendimento', ['positivita', 'gestione_errore', 'efficienza'])->nullable()->after('componente');
            }
            if (!Schema::hasColumn('esercizi', 'livello')) {
                $table->enum('livello', ['base', 'medio', 'alto'])->nullable()->after('rendimento');
            }
            if (!Schema::hasColumn('esercizi', 'n_giocatori')) {
                $table->string('n_giocatori', 10)->nullable()->after('livello');
            }
        });

        // Indici separati: se esistono già (run parziale) il try/catch evita il crash.
        foreach (['fase_gioco', 'componente', 'obiettivo'] as $col) {
            try {
                Schema::table('esercizi', fn (Blueprint $t) => $t->index($col));
            } catch (\Exception $e) {
                // Indice già presente — ok
            }
        }
    }

    public function down(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            $table->dropIndex(['fase_gioco']);
            $table->dropIndex(['componente']);
            $table->dropIndex(['obiettivo']);
            $table->dropColumn([
                'obiettivo', 'fase_seduta', 'fase_gioco',
                'componente', 'rendimento', 'livello', 'n_giocatori',
            ]);
        });
    }
};
