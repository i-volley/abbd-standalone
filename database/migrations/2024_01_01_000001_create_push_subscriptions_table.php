<?php

use Illuminate\Database\Migrations\Migration;

// Push subscriptions — pacchetto webpush non compatibile con L13, tabella omessa per PoC
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
