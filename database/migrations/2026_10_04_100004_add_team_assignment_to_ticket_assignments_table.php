<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_id']);
        });

        // A ticket_assignments row now represents EITHER a user assignment OR a team
        // assignment — assigned_to_id has to allow NULL for team-only rows.
        DB::statement('ALTER TABLE ticket_assignments MODIFY assigned_to_id BIGINT UNSIGNED NULL');

        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->foreign('assigned_to_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->after('assigned_to_id')->constrained('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_team_id');
            $table->dropForeign(['assigned_to_id']);
        });

        DB::statement('ALTER TABLE ticket_assignments MODIFY assigned_to_id BIGINT UNSIGNED NOT NULL');

        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->foreign('assigned_to_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
