<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esercizio_capacita', function (Blueprint $table) {
            $table->foreignId('esercizio_id')->constrained('esercizi')->cascadeOnDelete();
            $table->foreignId('capacita_id')->constrained('capacita')->cascadeOnDelete();
            $table->primary(['esercizio_id', 'capacita_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esercizio_capacita');
    }
};
