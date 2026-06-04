<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seduta_esercizi', function (Blueprint $table) {
            if (!Schema::hasColumn('seduta_esercizi', 'campo_id')) {
                $table->foreignId('campo_id')->nullable()->constrained('campi_seduta')->nullOnDelete();
            }
            if (!Schema::hasColumn('seduta_esercizi', 'n_salti')) {
                $table->unsignedSmallInteger('n_salti')->nullable();
            }
            if (!Schema::hasColumn('seduta_esercizi', 'minuti_lavoro')) {
                $table->unsignedSmallInteger('minuti_lavoro')->nullable();
            }
            if (!Schema::hasColumn('seduta_esercizi', 'carico_percepito')) {
                $table->unsignedTinyInteger('carico_percepito')->nullable();
            }
            if (!Schema::hasColumn('seduta_esercizi', 'fondamentale_id')) {
                $table->foreignId('fondamentale_id')->nullable()->constrained('gesti_tecnici')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('seduta_esercizi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campo_id');
            $table->dropConstrainedForeignId('fondamentale_id');
            $table->dropColumn(['n_salti', 'minuti_lavoro', 'carico_percepito']);
        });
    }
};
