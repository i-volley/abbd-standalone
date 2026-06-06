<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('feedback_questions')) {
            Schema::create('feedback_questions', function (Blueprint $table) {
                $table->id();
                $table->enum('paradigm', ['traditional','ecological','both']);
                $table->text('question_text');
                $table->enum('question_type', ['rating','text','boolean'])->default('text');
                $table->boolean('is_active')->default(true);
                $table->unsignedTinyInteger('position')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_questions');
    }
};
