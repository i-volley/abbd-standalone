<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('session_templates')) {
            Schema::create('session_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->enum('paradigm', ['traditional','ecological','hybrid']);
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(true)
                      ->comment('true = template di sistema non eliminabile');
                $table->foreignId('created_by')->nullable()
                      ->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('session_templates');
    }
};
