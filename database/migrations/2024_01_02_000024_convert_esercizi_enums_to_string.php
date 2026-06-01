<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Converte le colonne ENUM degli assi metodologici in stringhe.
     * Necessario perché i valori dei dropdown ora sono gestiti dall'allenatore
     * dalla tabella parametri_esercizio: un ENUM (CHECK constraint) rifiuterebbe
     * qualsiasi valore custom aggiunto dall'utente.
     */
    public function up(): void
    {
        // Colonne NOT NULL
        Schema::table('esercizi', function (Blueprint $table) {
            $table->string('fase', 40)->nullable(false)->change();
            $table->string('metodologia', 40)->nullable(false)->change();
        });

        // Colonne nullable (assi FIPAV opzionali)
        Schema::table('esercizi', function (Blueprint $table) {
            $table->string('obiettivo', 40)->nullable()->change();
            $table->string('fase_seduta', 40)->nullable()->change();
            $table->string('fase_gioco', 40)->nullable()->change();
            $table->string('componente', 40)->nullable()->change();
            $table->string('rendimento', 40)->nullable()->change();
            $table->string('livello', 40)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Ripristino enum originali
        Schema::table('esercizi', function (Blueprint $table) {
            $table->enum('fase', ['riscaldamento', 'potenziamento', 'stretching'])->nullable(false)->change();
            $table->enum('metodologia', ['analitico', 'sintetico', 'globale'])->nullable(false)->change();
        });
        Schema::table('esercizi', function (Blueprint $table) {
            $table->enum('obiettivo', ['permanente', 'principale', 'secondario'])->nullable()->change();
            $table->enum('fase_seduta', ['preparatoria', 'centrale', 'finale'])->nullable()->change();
            $table->enum('fase_gioco', ['cambio_palla', 'break_point', 'ricostruzione'])->nullable()->change();
            $table->enum('componente', ['tecnica', 'tattica'])->nullable()->change();
            $table->enum('rendimento', ['positivita', 'gestione_errore', 'efficienza'])->nullable()->change();
            $table->enum('livello', ['base', 'medio', 'alto'])->nullable()->change();
        });
    }
};
