<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('service_package_id')->nullable()->after('service')->constrained()->nullOnDelete();
            $table->decimal('subscription_price', 12, 2)->nullable()->after('service_package_id');
            $table->unsignedTinyInteger('billing_due_day')->default(10)->after('subscription_price');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_package_id');
            $table->dropColumn(['subscription_price', 'billing_due_day']);
        });
    }
};
