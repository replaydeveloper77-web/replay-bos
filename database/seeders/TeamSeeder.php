<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('username', 'superadmin')->first();

        if (! $superAdmin) {
            return;
        }

        $team = Team::updateOrCreate(
            ['name' => 'Tim Teknis'],
            ['description' => 'Tim penanganan teknis lapangan/troubleshooting', 'is_active' => true, 'created_by' => $superAdmin->id],
        );

        $memberIds = User::whereIn('username', ['noc-engineer-01', 'noc-engineer-02'])->pluck('id');
        $team->members()->syncWithoutDetaching($memberIds);
    }
}
