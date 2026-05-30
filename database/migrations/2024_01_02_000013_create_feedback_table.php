<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seduta_id')->constrained('sedute')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rpe');
            $table->tinyInteger('qualita_prestazione');
            $table->tinyInteger('impegno_squadra');
            $table->tinyInteger('miglioramento_fondamentale');
            $table->text('nota')->nullable();
            $table->boolean('inviato_in_scadenza')->default(true);
            $table->timestamps();

            $table->unique(['seduta_id', 'atleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
