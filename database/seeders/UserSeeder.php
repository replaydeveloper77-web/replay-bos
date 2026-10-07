<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['username' => 'noc-monitor-01', 'name' => 'NOC Monitor 01', 'role' => 'noc-monitor'],
            ['username' => 'noc-operator-01', 'name' => 'NOC Operator 01', 'role' => 'noc-operator'],
            ['username' => 'noc-engineer-01', 'name' => 'NOC Engineer 01', 'role' => 'noc-engineer'],
            ['username' => 'noc-engineer-02', 'name' => 'NOC Engineer 02', 'role' => 'noc-engineer'],
            ['username' => 'noc-admin-01', 'name' => 'NOC Admin 01', 'role' => 'noc-admin'],
            ['username' => 'marketing-01', 'name' => 'Marketing 01', 'role' => 'marketing'],
            ['username' => 'finance-01', 'name' => 'Finance 01', 'role' => 'finance'],
            ['username' => 'reseller-01', 'name' => 'Reseller 01', 'role' => 'reseller'],
            ['username' => 'hr-01', 'name' => 'HR 01', 'role' => 'hr'],
            ['username' => 'superadmin', 'name' => 'Super Admin', 'role' => 'super-admin'],
        ];

        $newCredentials = [];

        foreach ($accounts as $account) {
            $existing = User::where('username', $account['username'])->first();

            // Never regenerate a password for an account that already exists — re-running
            // this seeder (e.g. to add ONE new account) must not reset everyone else's login.
            if ($existing) {
                $user = $existing;
            } else {
                $password = Str::password(16, symbols: false);

                $user = User::create([
                    'username' => $account['username'],
                    'name' => $account['name'],
                    'email' => $account['username'].'@replay.net.id',
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]);

                $newCredentials[] = "{$account['username']} / {$password}";
            }

            $role = Role::where('slug', $account['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }

        if ($newCredentials) {
            $this->command?->info('=== akun BARU yang baru dibuat (catat sekarang, tidak ditampilkan lagi) ===');
            foreach ($newCredentials as $line) {
                $this->command?->line($line);
            }
        } else {
            $this->command?->info('=== tidak ada akun baru — semua username di daftar sudah ada, password tidak diubah ===');
        }
    }
}
