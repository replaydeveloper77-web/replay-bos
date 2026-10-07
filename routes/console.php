<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tagihan bulanan pelanggan — jalan otomatis tanggal 1 tiap bulan. Aman dijalankan
// berkali-kali: invoices:generate-monthly sendiri skip pelanggan yang sudah
// punya invoice RECURRING untuk periode yang sama.
Schedule::command('invoices:generate-monthly')->monthlyOn(1, '01:00');
