<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_esercizi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback')->cascadeOnDelete();
            $table->foreignId('seduta_esercizio_id')->constrained('seduta_esercizi')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('gradimento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_esercizi');
    }
};
