<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('troubleshooting_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->json('physical_checklist')->nullable();
            $table->json('logic_checklist')->nullable();
            $table->text('hypothesis')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('status')->default('OPEN');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('troubleshooting_sessions');
    }
};
