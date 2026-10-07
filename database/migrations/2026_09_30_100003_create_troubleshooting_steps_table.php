<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('troubleshooting_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('troubleshooting_sessions')->cascadeOnDelete();
            $table->string('step_type');
            $table->text('description');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('troubleshooting_steps');
    }
};
