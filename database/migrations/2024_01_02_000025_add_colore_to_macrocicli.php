<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('macrocicli', 'colore')) {
            Schema::table('macrocicli', function (Blueprint $table) {
                $table->string('colore', 9)->default('#4f46e5')->after('fase');
            });
        }
    }

    public function down(): void
    {
        Schema::table('macrocicli', function (Blueprint $table) {
            $table->dropColumn('colore');
        });
    }
};
