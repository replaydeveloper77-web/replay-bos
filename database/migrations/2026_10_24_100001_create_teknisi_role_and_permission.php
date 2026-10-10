<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $perm = Permission::updateOrCreate(
            ['slug' => 'teknisi.portal.view'],
            [
                'name' => 'Akses Portal Teknisi (lihat dashboard tugas work order & tiket lapangan)',
                'module' => 'teknisi',
            ],
        );

        $teknisiRole = Role::firstOrCreate(
            ['slug' => 'teknisi'],
            [
                'name' => 'Teknisi',
                'description' => 'Portal khusus teknisi lapangan — penanganan work order, tiket perbaikan, dan laporan kunjungan.',
            ],
        );

        // Berikan permission akses portal teknisi ke role teknisi & super-admin
        if (! $teknisiRole->permissions()->where('permissions.id', $perm->id)->exists()) {
            $teknisiRole->permissions()->attach($perm->id);
        }

        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin && ! $superAdmin->permissions()->where('permissions.id', $perm->id)->exists()) {
            $superAdmin->permissions()->attach($perm->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $perm = Permission::where('slug', 'teknisi.portal.view')->first();
        if ($perm) {
            $perm->roles()->detach();
            $perm->delete();
        }

        $role = Role::where('slug', 'teknisi')->first();
        if ($role) {
            $role->permissions()->detach();
            $role->users()->detach();
            $role->delete();
        }
    }
};
