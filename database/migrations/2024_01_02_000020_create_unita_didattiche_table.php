<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Manuale FIPAV Primo Grado, Metodologia 1-6:
        // "Obiettivo permanente costante — obiettivo principale variabile"
        // "Numero di sedute necessarie in base agli obiettivi tecnici"
        Schema::create('unita_didattiche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('allenatore_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('microciclo_id')->nullable()->constrained('microcicli')->nullOnDelete();
            $table->string('titolo');
            $table->text('obiettivo_permanente');               // costante per tutte le sedute
            $table->enum('progressione', [
                'analitico_globale',  // analitico → sintetico → globale
                'sintetico_globale',  // sintetico → globale
                'libera',             // sequenza libera
            ])->default('analitico_globale');
            $table->date('data_inizio')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('team_id');
            $table->index('allenatore_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unita_didattiche');
    }
};
