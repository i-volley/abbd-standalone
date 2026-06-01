<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabella lookup per i parametri dei menu della scheda esercizio.
        // tipo = fase | metodologia | obiettivo | fase_seduta | fase_gioco
        //      | componente | rendimento | livello
        // Permette all'allenatore di gestire (CRUD) le voci dei dropdown.
        if (Schema::hasTable('parametri_esercizio')) {
            return;
        }
        Schema::create('parametri_esercizio', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 40);          // categoria del parametro
            $table->string('valore', 60);        // valore macchina salvato su esercizi
            $table->string('etichetta', 120);    // label mostrata in UI
            $table->string('colore', 9)->nullable();  // HEX opzionale per badge
            $table->integer('ordinamento')->default(0);
            $table->boolean('attivo')->default(true);
            $table->boolean('di_sistema')->default(false); // default FIPAV non eliminabili
            $table->timestamps();

            $table->unique(['tipo', 'valore']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametri_esercizio');
    }
};
