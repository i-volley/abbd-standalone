<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('session_template_blocks')) {
            Schema::create('session_template_blocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_template_id')
                      ->constrained('session_templates')->cascadeOnDelete();
                $table->unsignedTinyInteger('position')->comment('Ordine del blocco nella seduta');
                $table->string('block_name', 100);
                $table->text('block_description')->nullable();
                $table->unsignedTinyInteger('suggested_duration_minutes')->nullable();
                $table->enum('block_type', [
                    'warmup','technical','tactical',
                    'ecological_constraint','game_form','cooldown','free'
                ])->default('free');
                $table->enum('constraint_focus', ['organism','task','environment','none'])
                      ->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('session_template_blocks');
    }
};
