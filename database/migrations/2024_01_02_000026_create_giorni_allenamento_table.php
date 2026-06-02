<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('giorni_allenamento')) return;

        Schema::create('giorni_allenamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagione_id')->constrained('stagioni')->cascadeOnDelete();
            $table->tinyInteger('giorno_settimana'); // 0=Dom, 1=Lun, ..., 6=Sab
            $table->time('ora_inizio');
            $table->time('ora_fine')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giorni_allenamento');
    }
};
