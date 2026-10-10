<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlignCustomerServiceNumbers extends Command
{
    protected $signature = 'customers:align-service-numbers 
                            {--category=RESELLER : Kategori pelanggan (RESELLER, CORPORATE, HOME, atau ALL)} 
                            {--dry-run : Simulasi penyelarasan tanpa menyimpan perubahan ke database}';

    protected $description = 'Menyelaraskan urutan nomor layanan pelanggan (misal: 920210000[urut]) agar berurutan rapi tanpa ada nomor yang terlewat.';

    public function handle(AuditService $auditService): int
    {
        $categoryInput = strtoupper($this->option('category') ?: 'RESELLER');
        $isDryRun = (bool) $this->option('dry-run');

        $categories = match ($categoryInput) {
            'ALL' => ['RESELLER', 'CORPORATE', 'HOME'],
            'RESELLER', 'CORPORATE', 'HOME' => [$categoryInput],
            default => null,
        };

        if (! $categories) {
            $this->error("Kategori '{$categoryInput}' tidak valid. Pilihan valid: RESELLER, CORPORATE, HOME, ALL.");
            return self::FAILURE;
        }

        if ($isDryRun) {
            $this->warn("MODUS SIMULASI (DRY-RUN): Tidak ada perubahan yang akan disimpan ke database.");
        }

        $systemActor = User::whereHas('roles', fn ($q) => $q->where('slug', 'super-admin'))->oldest('id')->first()
            ?? User::oldest('id')->first();

        $totalUpdated = 0;

        foreach ($categories as $cat) {
            $prefix = match ($cat) {
                'RESELLER' => Customer::SERVICE_PREFIX_RESELLER,
                'CORPORATE' => Customer::SERVICE_PREFIX_CORPORATE,
                default => Customer::SERVICE_PREFIX_HOME,
            };

            $customers = Customer::where('category', $cat)->orderBy('id', 'asc')->get();

            if ($customers->isEmpty()) {
                $this->line("Tidak ada pelanggan untuk kategori <comment>{$cat}</comment>.");
                continue;
            }

            $this->info("Memeriksa penyelarasan nomor layanan kategori: {$cat} (Total: {$customers->count()} pelanggan)...");

            $changes = [];
            $seq = 1;

            DB::beginTransaction();
            try {
                foreach ($customers as $customer) {
                    $expectedNumber = $prefix . $seq;
                    $currentNumber = $customer->service_number;

                    if ($currentNumber !== $expectedNumber) {
                        $changes[] = [
                            'id' => $customer->id,
                            'name' => $customer->name,
                            'current' => $currentNumber,
                            'new' => $expectedNumber,
                        ];

                        if (! $isDryRun) {
                            $oldVal = ['service_number' => $currentNumber];
                            $customer->update(['service_number' => $expectedNumber]);

                            if ($systemActor) {
                                $auditService->log(
                                    actor: $systemActor,
                                    request: Request::create('/', 'GET'),
                                    module: 'CUSTOMER',
                                    action: 'ALIGN_SERVICE_NUMBER',
                                    target: 'Customer',
                                    targetId: $customer->id,
                                    oldValue: $oldVal,
                                    newValue: ['service_number' => $expectedNumber],
                                );
                            }
                        }
                    }

                    $seq++;
                }

                if (! $isDryRun) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Gagal menyelaraskan kategori {$cat}: " . $e->getMessage());
                return self::FAILURE;
            }

            if (empty($changes)) {
                $this->line("✅ Seluruh nomor layanan kategori <info>{$cat}</info> sudah selaras dan rapi.");
            } else {
                $this->table(
                    ['ID', 'Nama Pelanggan', 'Nomor Lama', 'Nomor Baru'],
                    array_map(fn ($c) => [$c['id'], $c['name'], $c['current'], $c['new']], $changes)
                );

                $count = count($changes);
                $totalUpdated += $count;

                if ($isDryRun) {
                    $this->warn("Terdapat {$count} pelanggan kategori {$cat} yang perlu diselaraskan.");
                } else {
                    $this->info("Berhasil menyelaraskan {$count} pelanggan kategori {$cat}.");
                }
            }
        }

        if ($isDryRun) {
            $this->comment("Simulasi selesai. Jalankan tanpa --dry-run untuk menerapkan ke database.");
        } else {
            $this->info("Selesai! Total {$totalUpdated} nomor layanan berhasil diperbarui.");
        }

        return self::SUCCESS;
    }
}
