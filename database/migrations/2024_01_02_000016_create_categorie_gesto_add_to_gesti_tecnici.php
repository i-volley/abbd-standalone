<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie_gesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->string('nome');
            $table->string('colore', 20)->default('#6c757d');
            $table->integer('ordinamento')->default(0);
            $table->timestamps();
        });

        Schema::table('gesti_tecnici', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()
                  ->constrained('categorie_gesto')->nullOnDelete()
                  ->after('sport_id');
        });
    }

    public function down(): void
    {
        Schema::table('gesti_tecnici', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
        Schema::dropIfExists('categorie_gesto');
    }
};
