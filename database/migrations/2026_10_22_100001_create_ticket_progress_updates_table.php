<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('note');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->foreignId('ticket_progress_update_id')
                ->nullable()
                ->after('uploaded_by')
                ->constrained('ticket_progress_updates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_progress_update_id');
        });

        Schema::dropIfExists('ticket_progress_updates');
    }
};
