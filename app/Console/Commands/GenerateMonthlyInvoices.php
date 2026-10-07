<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly {--period= : Bulan yang ditagih, format YYYY-MM. Default: bulan berjalan.}';

    protected $description = 'Generate tagihan bulanan untuk semua pelanggan dengan langganan aktif (dijalankan otomatis tiap tanggal 1, bisa juga dipicu manual).';

    public function handle(InvoiceService $invoiceService): int
    {
        $period = $this->option('period') ? Carbon::parse($this->option('period').'-01') : now()->startOfMonth();

        // System actor for the audit trail — the oldest super-admin account, since
        // this can run from cron with no logged-in user.
        $systemActor = User::whereHas('roles', fn ($q) => $q->where('slug', 'super-admin'))->oldest('id')->first();

        if (! $systemActor) {
            $this->error('Tidak ada akun Super Admin untuk dipakai sebagai actor audit trail. Generate dibatalkan.');

            return self::FAILURE;
        }

        $invoices = $invoiceService->generateMonthly($period, $systemActor, Request::create('/', 'GET'));

        $this->info("Berhasil generate {$invoices->count()} invoice untuk periode {$period->translatedFormat('F Y')}.");

        return self::SUCCESS;
    }
}
