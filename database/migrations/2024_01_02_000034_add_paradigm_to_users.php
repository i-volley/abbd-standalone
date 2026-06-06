<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'paradigm')) {
                $table->enum('paradigm', ['traditional','ecological','hybrid'])
                      ->default('traditional')->after('email');
            }
            if (!Schema::hasColumn('users', 'paradigm_weight_ecological')) {
                $table->unsignedTinyInteger('paradigm_weight_ecological')
                      ->default(0)->after('paradigm')
                      ->comment('0-100, usato solo se paradigm=hybrid');
            }
            if (!Schema::hasColumn('users', 'feedback_style')) {
                $table->enum('feedback_style', ['prescriptive','interrogative','mixed'])
                      ->default('prescriptive')->after('paradigm_weight_ecological');
            }
            if (!Schema::hasColumn('users', 'ai_suggestion_tone')) {
                $table->enum('ai_suggestion_tone', ['directive','explorative','neutral'])
                      ->default('directive')->after('feedback_style');
            }
            if (!Schema::hasColumn('users', 'preferred_session_blocks')) {
                $table->unsignedTinyInteger('preferred_session_blocks')
                      ->default(6)->after('ai_suggestion_tone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'paradigm', 'paradigm_weight_ecological',
                'feedback_style', 'ai_suggestion_tone', 'preferred_session_blocks',
            ]);
        });
    }
};
