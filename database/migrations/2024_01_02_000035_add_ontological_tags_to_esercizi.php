<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            if (!Schema::hasColumn('esercizi', 'exercise_category')) {
                $table->enum('exercise_category', ['analytic','situational','game_form','free_play'])
                      ->default('analytic')->after('prevenzione_distretto');
            }
            if (!Schema::hasColumn('esercizi', 'paradigm_primary')) {
                $table->enum('paradigm_primary', ['traditional','ecological','neutral'])
                      ->default('neutral')->after('exercise_category');
            }
            if (!Schema::hasColumn('esercizi', 'constraint_type')) {
                $table->enum('constraint_type', ['organism','task','environment','none'])
                      ->default('none')->after('paradigm_primary');
            }
            if (!Schema::hasColumn('esercizi', 'representativeness')) {
                $table->enum('representativeness', ['low','medium','high'])
                      ->default('low')->after('constraint_type');
            }
            if (!Schema::hasColumn('esercizi', 'feedback_suggestion')) {
                $table->enum('feedback_suggestion', ['corrective_technical','interrogative_perceptual','neutral'])
                      ->default('neutral')->after('representativeness');
            }
            if (!Schema::hasColumn('esercizi', 'affordance_targets')) {
                $table->json('affordance_targets')->nullable()->after('feedback_suggestion')
                      ->comment('Array: space, time, teammate, opponent, ball, net');
            }
        });
    }

    public function down(): void
    {
        Schema::table('esercizi', function (Blueprint $table) {
            $table->dropColumn([
                'exercise_category', 'paradigm_primary', 'constraint_type',
                'representativeness', 'feedback_suggestion', 'affordance_targets',
            ]);
        });
    }
};
