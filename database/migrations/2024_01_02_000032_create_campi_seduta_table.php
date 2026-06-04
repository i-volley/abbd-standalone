<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('campi_seduta')) {
            Schema::create('campi_seduta', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seduta_id')->constrained('sedute')->cascadeOnDelete();
                $table->string('nome', 100)->default('Campo 1');
                $table->string('colore', 20)->nullable()->default('#3b82f6');
                $table->unsignedSmallInteger('ordine')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campi_seduta');
    }
};
