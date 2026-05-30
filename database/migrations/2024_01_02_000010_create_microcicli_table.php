<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microcicli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('macrociclo_id')->constrained('macrocicli')->cascadeOnDelete();
            $table->integer('numero');
            $table->date('data_inizio');
            $table->enum('intensita', ['bassa', 'media', 'alta', 'scarico']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microcicli');
    }
};
