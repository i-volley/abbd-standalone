<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('giorni_allenamento', function (Blueprint $table) {
            if (!Schema::hasColumn('giorni_allenamento', 'tipo_allenamento_id')) {
                $table->foreignId('tipo_allenamento_id')->nullable()->constrained('tipo_allenamenti')->nullOnDelete();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'indirizzo')) {
                $table->string('indirizzo', 255)->nullable();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'citta')) {
                $table->string('citta', 100)->nullable();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'ora_ritrovo')) {
                $table->time('ora_ritrovo')->nullable();
            }
            if (!Schema::hasColumn('giorni_allenamento', 'note_ritrovo')) {
                $table->string('note_ritrovo', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('giorni_allenamento', function (Blueprint $table) {
            $table->dropColumn(['tipo_allenamento_id', 'indirizzo', 'citta', 'lat', 'lng', 'ora_ritrovo', 'note_ritrovo']);
        });
    }
};
