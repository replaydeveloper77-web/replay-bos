<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PackageChangeRequest;
use App\Models\Role;
use App\Models\ServicePackage;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $financeUser;
    private User $marketingUser;
    private User $unauthorizedUser;
    private Customer $customer;
    private ServicePackage $packageA;
    private ServicePackage $packageB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $financeRole = Role::where('slug', 'finance')->first();
        $marketingRole = Role::where('slug', 'marketing')->first();

        $this->superAdmin = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->attach($superAdminRole->id);

        $this->financeUser = User::create([
            'username' => 'finance1',
            'name' => 'Siti Finance',
            'email' => 'siti.finance@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->financeUser->roles()->attach($financeRole->id);

        $this->marketingUser = User::create([
            'username' => 'marketing1',
            'name' => 'Joko Marketing',
            'email' => 'joko.marketing@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->marketingUser->roles()->attach($marketingRole->id);

        $this->unauthorizedUser = User::create([
            'username' => 'guestuser',
            'name' => 'Guest User',
            'email' => 'guest@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->packageA = ServicePackage::create([
            'name' => 'Paket Basic 20 Mbps',
            'speed_label' => '20 Mbps',
            'price' => 250000,
            'is_active' => true,
            'created_by' => $this->superAdmin->id,
        ]);

        $this->packageB = ServicePackage::create([
            'name' => 'Paket Pro 50 Mbps',
            'speed_label' => '50 Mbps',
            'price' => 450000,
            'is_active' => true,
            'created_by' => $this->superAdmin->id,
        ]);

        $this->customer = Customer::create([
            'name' => 'PT Sumber Rejeki',
            'category' => 'CORPORATE',
            'service_number' => '9202200001',
            'service_package_id' => $this->packageA->id,
            'subscription_price' => 250000,
            'service' => $this->packageA->name,
            'is_active' => true,
            'created_by' => $this->superAdmin->id,
        ]);
    }

    public function test_user_can_view_package_changes_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('package-changes.index'));
        $response->assertStatus(200);
        $response->assertSee('Permohonan Upgrade / Downgrade Paket');
        $response->assertSee('Upgrade/Downgrade');
    }

    public function test_user_can_view_create_form_with_category_filters(): void
    {
        $response = $this->actingAs($this->marketingUser)->get(route('package-changes.create'));
        $response->assertStatus(200);
        $response->assertSee('Pilih Kategori Client / Pelanggan');
        $response->assertSee('client_category_filter');
        $response->assertSee('RESELLER');
        $response->assertSee('CORPORATE');
        $response->assertSee('HOME');
        $response->assertSee('Paket Baru Tujuan');
    }

    public function test_unauthorized_user_cannot_access_package_changes(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('package-changes.index'));
        $response->assertStatus(403);
    }

    public function test_marketing_can_submit_upgrade_request_with_typed_package(): void
    {
        $response = $this->actingAs($this->marketingUser)->post(route('package-changes.store'), [
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'target_package' => 'Paket Custom Fiber 100 Mbps',
            'notes' => 'Pelanggan minta upgrade untuk event akhir tahun.',
        ]);

        $response->assertRedirect(route('package-changes.index'));
        $this->assertDatabaseHas('package_change_requests', [
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'current_service_package_id' => $this->packageA->id,
            'target_package_name' => 'Paket Custom Fiber 100 Mbps',
            'status' => 'PENDING',
            'requested_by' => $this->marketingUser->id,
        ]);
    }

    public function test_marketing_can_submit_upgrade_request_with_catalog_package_name(): void
    {
        $response = $this->actingAs($this->marketingUser)->post(route('package-changes.store'), [
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'target_package' => 'Paket Pro 50 Mbps',
            'notes' => 'Upgrade ke paket pro katalog',
        ]);

        $response->assertRedirect(route('package-changes.index'));
        $this->assertDatabaseHas('package_change_requests', [
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'target_package_name' => 'Paket Pro 50 Mbps',
            'target_service_package_id' => $this->packageB->id,
            'target_price' => 450000,
            'status' => 'PENDING',
        ]);
    }

    public function test_finance_approval_automatically_generates_work_order_and_updates_customer(): void
    {
        // 1. Buat pengajuan upgrade dengan nama paket diketik
        $changeRequest = PackageChangeRequest::create([
            'request_number' => 'REQ-UPG-000001',
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'current_service_package_id' => $this->packageA->id,
            'current_package_name' => $this->packageA->name,
            'current_price' => 250000,
            'target_package_name' => 'Paket Dedicated 100 Mbps',
            'notes' => 'Kebutuhan bandwidth meeting zoom',
            'status' => 'PENDING',
            'requested_by' => $this->marketingUser->id,
        ]);

        // 2. Finance menyetujui pengajuan dengan penyesuaian tarif baru
        $response = $this->actingAs($this->financeUser)->post(route('package-changes.approve', $changeRequest), [
            'finance_notes' => 'Disetujui. Tagihan bulan depan disesuaikan.',
            'new_price' => 600000,
        ]);

        $response->assertRedirect();
        $changeRequest->refresh();

        // 3. Verifikasi status pengajuan berubah jadi APPROVED
        $this->assertEquals('APPROVED', $changeRequest->status);
        $this->assertEquals($this->financeUser->id, $changeRequest->approved_by);
        $this->assertEquals(600000, (float) $changeRequest->target_price);
        $this->assertNotNull($changeRequest->approved_at);
        $this->assertNotNull($changeRequest->work_order_id);

        // 4. Verifikasi Work Order otomatis digenerate
        $this->assertDatabaseHas('work_orders', [
            'id' => $changeRequest->work_order_id,
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'status' => 'SCHEDULED',
        ]);

        $workOrder = $changeRequest->workOrder;
        $this->assertNotNull($workOrder);
        $this->assertStringStartsWith('WO-', $workOrder->work_order_number);

        // 5. Verifikasi master pelanggan otomatis terupdate paket dan harga barunya
        $this->customer->refresh();
        $this->assertEquals(600000, (float) $this->customer->subscription_price);
        $this->assertEquals('Paket Dedicated 100 Mbps', $this->customer->service);
    }

    public function test_finance_can_reject_upgrade_request(): void
    {
        $changeRequest = PackageChangeRequest::create([
            'request_number' => 'REQ-UPG-000002',
            'customer_id' => $this->customer->id,
            'type' => 'UPGRADE',
            'current_service_package_id' => $this->packageA->id,
            'current_package_name' => $this->packageA->name,
            'current_price' => 250000,
            'target_package_name' => $this->packageB->name,
            'target_service_package_id' => $this->packageB->id,
            'target_price' => 450000,
            'status' => 'PENDING',
            'requested_by' => $this->marketingUser->id,
        ]);

        $response = $this->actingAs($this->financeUser)->post(route('package-changes.reject', $changeRequest), [
            'finance_notes' => 'Ada tunggakan bulan lalu yang belum lunas.',
        ]);

        $response->assertRedirect();
        $changeRequest->refresh();

        $this->assertEquals('REJECTED', $changeRequest->status);
        $this->assertEquals('Ada tunggakan bulan lalu yang belum lunas.', $changeRequest->finance_notes);
        $this->assertNull($changeRequest->work_order_id);

        // Customer tidak berubah
        $this->customer->refresh();
        $this->assertEquals($this->packageA->id, $this->customer->service_package_id);
    }
}
