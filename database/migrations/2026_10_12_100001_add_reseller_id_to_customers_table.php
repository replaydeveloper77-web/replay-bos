<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Self-referencing: only meaningful when category = HOME. restrictOnDelete
            // so a Reseller can't be hard-deleted while Home customers still point to it
            // (on top of the existing hasHistory() app-level check).
            $table->foreignId('reseller_id')->nullable()->after('category')
                ->constrained('customers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reseller_id');
        });
    }
};
