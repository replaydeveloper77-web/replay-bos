<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // quotation_id must become nullable (recurring/manual invoices have no quotation behind them).
        // ->nullable()->change() needs doctrine/dbal, which isn't installed — drop the FK, alter the
        // column with raw SQL, then re-add the FK, same approach used for ticket_assignments earlier.
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE invoices MODIFY quotation_id BIGINT UNSIGNED NULL');
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();

            $table->string('type')->default('CLOSING')->after('customer_id');
            $table->string('billing_period')->nullable()->after('type');
            $table->date('due_date')->nullable()->after('billing_period');
            $table->string('status')->default('UNPAID')->after('total');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->foreignId('paid_by')->nullable()->after('paid_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_by');
            $table->dropColumn(['type', 'billing_period', 'due_date', 'status', 'paid_at']);
        });
    }
};
