<?php

namespace Database\Seeders;

use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            ['priority' => 'CRITICAL', 'response_minutes' => 15, 'description' => 'Response dalam 15 menit'],
            ['priority' => 'HIGH', 'response_minutes' => 30, 'description' => 'Response dalam 30 menit'],
            ['priority' => 'MEDIUM', 'response_minutes' => 120, 'description' => 'Response dalam 2 jam'],
            ['priority' => 'LOW', 'response_minutes' => 480, 'description' => 'Response dalam 8 jam'],
        ];

        foreach ($policies as $policy) {
            SlaPolicy::updateOrCreate(['priority' => $policy['priority']], $policy);
        }
    }
}
