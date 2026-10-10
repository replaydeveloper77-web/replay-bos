<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('type')->default('UPGRADE'); // UPGRADE or DOWNGRADE
            $table->foreignId('current_service_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
            $table->string('current_package_name')->nullable();
            $table->decimal('current_price', 15, 2)->nullable();
            $table->foreignId('target_service_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
            $table->string('target_package_name');
            $table->decimal('target_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED, CANCELLED
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('finance_notes')->nullable();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->timestamps();
        });

        // Register new permissions
        $permissions = [
            ['package', 'package.change.view', 'Lihat Permohonan Upgrade/Downgrade Paket'],
            ['package', 'package.change.create', 'Ajukan Permohonan Upgrade/Downgrade Paket'],
            ['package', 'package.change.approve', 'Persetujuan (Approve/Reject) Upgrade/Downgrade Paket (Finance)'],
        ];

        foreach ($permissions as [$module, $slug, $name]) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'module' => $module]
            );
        }

        // Attach permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching(
                Permission::whereIn('slug', ['package.change.view', 'package.change.create', 'package.change.approve'])->pluck('id')
            );
        }

        $finance = Role::where('slug', 'finance')->first();
        if ($finance) {
            $finance->permissions()->syncWithoutDetaching(
                Permission::whereIn('slug', ['package.change.view', 'package.change.approve'])->pluck('id')
            );
        }

        $marketing = Role::where('slug', 'marketing')->first();
        if ($marketing) {
            $marketing->permissions()->syncWithoutDetaching(
                Permission::whereIn('slug', ['package.change.view', 'package.change.create'])->pluck('id')
            );
        }

        $nocAdmin = Role::where('slug', 'noc-admin')->first();
        if ($nocAdmin) {
            $nocAdmin->permissions()->syncWithoutDetaching(
                Permission::whereIn('slug', ['package.change.view', 'package.change.create'])->pluck('id')
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_change_requests');

        Permission::whereIn('slug', [
            'package.change.view',
            'package.change.create',
            'package.change.approve',
        ])->delete();
    }
};
