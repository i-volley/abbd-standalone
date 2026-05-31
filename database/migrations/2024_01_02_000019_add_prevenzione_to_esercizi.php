<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            // Prevenzione distretto: da Manuale FIPAV Primo Grado, Metodologia 3-12/13/14/15
            $table->enum('prevenzione_distretto', ['caviglia', 'ginocchio', 'lombare', 'spalla'])
                  ->nullable()
                  ->after('n_giocatori');
            $table->index('prevenzione_distretto');
        });
    }

    public function down(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            $table->dropIndex(['prevenzione_distretto']);
            $table->dropColumn('prevenzione_distretto');
        });
    }
};
