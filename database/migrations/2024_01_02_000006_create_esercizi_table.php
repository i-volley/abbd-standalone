<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esercizi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->foreignId('gesto_tecnico_id')->nullable()->constrained('gesti_tecnici')->nullOnDelete();
            $table->foreignId('creato_da')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('fase', ['riscaldamento', 'potenziamento', 'stretching']);
            $table->enum('metodologia', ['analitico', 'sintetico', 'globale']);
            $table->integer('n_salti')->default(0);
            $table->integer('n_gesti')->default(0);
            $table->integer('durata_min')->default(5);
            $table->string('video_url')->nullable();
            $table->text('descrizione')->nullable();
            $table->timestamps();

            $table->index('sport_id');
            $table->index('gesto_tecnico_id');
            $table->index('fase');
            $table->index('metodologia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esercizi');
    }
};
