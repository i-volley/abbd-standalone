<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacita', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['motoria', 'cognitiva']);
            $table->string('nome');
            $table->string('colore', 7)->default('#6c757d');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacita');
    }
};
