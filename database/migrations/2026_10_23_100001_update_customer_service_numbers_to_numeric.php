<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $customers = DB::table('customers')
            ->whereNotNull('service_number')
            ->get();

        foreach ($customers as $c) {
            $oldNum = trim((string) $c->service_number);
            $newNum = null;

            if (preg_match('/^RS-(\d+)$/i', $oldNum, $m)) {
                $newNum = Customer::SERVICE_PREFIX_RESELLER . (int) $m[1];
            } elseif (preg_match('/^CP-(\d+)$/i', $oldNum, $m)) {
                $newNum = Customer::SERVICE_PREFIX_CORPORATE . (int) $m[1];
            } elseif (preg_match('/^PLG-(\d+)$/i', $oldNum, $m)) {
                $newNum = Customer::SERVICE_PREFIX_HOME . (int) $m[1];
            } elseif (preg_match('/^92021(\d+)$/', $oldNum, $m) && ! str_starts_with($oldNum, Customer::SERVICE_PREFIX_RESELLER)) {
                $newNum = Customer::SERVICE_PREFIX_RESELLER . (int) $m[1];
            } elseif (preg_match('/^92022(\d+)$/', $oldNum, $m) && ! str_starts_with($oldNum, Customer::SERVICE_PREFIX_CORPORATE)) {
                $newNum = Customer::SERVICE_PREFIX_CORPORATE . (int) $m[1];
            } elseif (preg_match('/^92023(\d+)$/', $oldNum, $m) && ! str_starts_with($oldNum, Customer::SERVICE_PREFIX_HOME)) {
                $newNum = Customer::SERVICE_PREFIX_HOME . (int) $m[1];
            }

            if ($newNum && $newNum !== $oldNum) {
                DB::table('customers')
                    ->where('id', $c->id)
                    ->update(['service_number' => $newNum]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep updated numeric service numbers
    }
};
