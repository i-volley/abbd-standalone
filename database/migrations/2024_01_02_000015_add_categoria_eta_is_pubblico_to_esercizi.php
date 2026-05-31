<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            $table->string('categoria_eta', 20)->nullable()->after('descrizione');
            $table->boolean('is_pubblico')->default(false)->after('categoria_eta');
        });
    }

    public function down(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            $table->dropColumn(['categoria_eta', 'is_pubblico']);
        });
    }
};
