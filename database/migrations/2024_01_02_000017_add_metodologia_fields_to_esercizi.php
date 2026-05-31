<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            // Assi metodologici dal Manuale FIPAV Primo Grado (vedi docs/metodologia-eserciziario.md)
            $table->enum('obiettivo', ['permanente', 'principale', 'secondario'])->nullable()->after('metodologia');
            $table->enum('fase_seduta', ['preparatoria', 'centrale', 'finale'])->nullable()->after('obiettivo');
            $table->enum('fase_gioco', ['cambio_palla', 'break_point', 'ricostruzione'])->nullable()->after('fase_seduta');
            $table->enum('componente', ['tecnica', 'tattica'])->nullable()->after('fase_gioco');
            $table->enum('rendimento', ['positivita', 'gestione_errore', 'efficienza'])->nullable()->after('componente');
            $table->enum('livello', ['base', 'medio', 'alto'])->nullable()->after('rendimento');
            $table->string('n_giocatori', 10)->nullable()->after('livello');

            $table->index('fase_gioco');
            $table->index('componente');
            $table->index('obiettivo');
        });
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
