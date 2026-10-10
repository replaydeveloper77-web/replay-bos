<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::updateOrCreate(
            ['slug' => 'database.monitor'],
            [
                'name' => 'Monitoring Resource Database & Server (cPanel)',
                'module' => 'database',
            ]
        );

        // Grant to super-admin and noc-admin by default
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $nocAdmin = Role::where('slug', 'noc-admin')->first();
        if ($nocAdmin) {
            $nocAdmin->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    public function down(): void
    {
        Permission::where('slug', 'database.monitor')->delete();
    }
};
