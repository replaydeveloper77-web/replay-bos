<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('prospect_name');
            $table->string('prospect_company')->nullable();
            $table->string('prospect_address')->nullable();
            $table->string('prospect_phone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_covered')->nullable();
            $table->decimal('nearest_distance_km', 6, 2)->nullable();
            $table->string('nearest_point_name')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('ppn_rate', 5, 2)->default(11);
            $table->decimal('ppn_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('DRAFT');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
