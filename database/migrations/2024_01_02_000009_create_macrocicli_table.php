<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('macrocicli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagione_id')->constrained('stagioni')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('fase', ['preparazione', 'competizione', 'transizione']);
            $table->text('obiettivi')->nullable();
            $table->date('data_inizio');
            $table->date('data_fine');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('macrocicli');
    }
};
