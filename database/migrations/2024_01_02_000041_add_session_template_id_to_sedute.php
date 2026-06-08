<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            $table->foreignId('session_template_id')
                  ->nullable()
                  ->after('microciclo_id')
                  ->constrained('session_templates')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sedute', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SessionTemplate::class);
            $table->dropColumn('session_template_id');
        });
    }
};
