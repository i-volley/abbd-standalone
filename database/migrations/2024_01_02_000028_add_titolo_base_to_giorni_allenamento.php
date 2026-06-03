<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('giorni_allenamento') && !Schema::hasColumn('giorni_allenamento', 'titolo_base')) {
            Schema::table('giorni_allenamento', function (Blueprint $table) {
                $table->string('titolo_base')->default('Allenamento')->after('giorno_settimana');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('giorni_allenamento', 'titolo_base')) {
            Schema::table('giorni_allenamento', fn($t) => $t->dropColumn('titolo_base'));
        }
    }
};
