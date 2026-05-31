<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Track paralleli per ruolo: Manuale FIPAV Primo Grado, Metodologia 1-9
        // "Differenziazione e individualizzazione del lavoro"
        // "Alzatori e liberi lavorano separatamente durante la fase preparatoria"
        if (!Schema::hasColumn('seduta_esercizi', 'track')) {
            Schema::table('seduta_esercizi', function (Blueprint $table) {
                $table->enum('track', [
                    'completo',              // tutta la squadra (default)
                    'alzatore',
                    'ricevitore_attaccante',
                    'centrale',
                    'opposto',
                    'libero',
                ])->default('completo')->after('ordinamento');
            });
        }
        try {
            Schema::table('seduta_esercizi', fn (Blueprint $t) => $t->index('track'));
        } catch (\Exception $e) {
            // Indice già presente — ok
        }
    }

    public function down(): void
    {
        Schema::table('seduta_esercizi', function (Blueprint $table) {
            $table->dropIndex(['track']);
            $table->dropColumn('track');
        });
    }
};
