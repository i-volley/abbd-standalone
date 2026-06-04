<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            if (!Schema::hasColumn('sedute', 'n_atlete')) {
                $table->unsignedSmallInteger('n_atlete')->nullable();
            }
            if (!Schema::hasColumn('sedute', 'obiettivo_principale')) {
                $table->string('obiettivo_principale', 255)->nullable();
            }
            if (!Schema::hasColumn('sedute', 'obiettivo_secondario')) {
                $table->string('obiettivo_secondario', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            $table->dropColumn(['n_atlete', 'obiettivo_principale', 'obiettivo_secondario']);
        });
    }
};
