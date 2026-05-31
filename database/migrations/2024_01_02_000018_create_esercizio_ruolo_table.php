<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot multi-ruolo: un esercizio puo' valere per piu' ruoli.
        // Assenza di righe = esercizio generico (vale per tutti i ruoli).
        if (!Schema::hasTable('esercizio_ruolo')) {
            Schema::create('esercizio_ruolo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('esercizio_id')->constrained('esercizi')->cascadeOnDelete();
                $table->enum('ruolo', [
                    'alzatore',
                    'ricevitore_attaccante',
                    'centrale',
                    'opposto',
                    'libero',
                ]);
                $table->timestamps();

                $table->unique(['esercizio_id', 'ruolo']);
                $table->index('ruolo');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('esercizio_ruolo');
    }
};
