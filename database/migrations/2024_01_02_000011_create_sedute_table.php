<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('microciclo_id')->nullable()->constrained('microcicli')->nullOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('allenatore_id')->constrained('users')->cascadeOnDelete();
            $table->string('titolo');
            $table->date('data');
            $table->integer('durata_tot_min')->default(0);
            $table->enum('stato', ['bozza', 'pubblicata', 'completata'])->default('bozza');
            $table->boolean('visibile_atleti')->default(false);
            $table->dateTime('scadenza_feedback')->nullable();
            $table->boolean('reminder_inviato')->default(false);
            $table->text('note_allenatore')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedute');
    }
};
