<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seduta_esercizi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seduta_id')->constrained('sedute')->cascadeOnDelete();
            $table->foreignId('esercizio_id')->constrained('esercizi')->cascadeOnDelete();
            $table->integer('ordinamento')->default(0);
            $table->integer('serie')->nullable();
            $table->integer('ripetizioni')->nullable();
            $table->integer('recupero_sec')->nullable();
            $table->boolean('voto_abilitato')->default(false);
            $table->string('note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seduta_esercizi');
    }
};
