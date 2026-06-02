<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sedute') && !Schema::hasColumn('sedute', 'luogo')) {
            Schema::table('sedute', function (Blueprint $table) {
                $table->string('luogo')->nullable()->after('data');
            });
        }

        if (Schema::hasTable('giorni_allenamento') && !Schema::hasColumn('giorni_allenamento', 'luogo')) {
            Schema::table('giorni_allenamento', function (Blueprint $table) {
                $table->string('luogo')->nullable()->after('note');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sedute', 'luogo')) {
            Schema::table('sedute', fn($t) => $t->dropColumn('luogo'));
        }
        if (Schema::hasColumn('giorni_allenamento', 'luogo')) {
            Schema::table('giorni_allenamento', fn($t) => $t->dropColumn('luogo'));
        }
    }
};
