<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTeknisiTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $teknisiUser;
    private User $unauthorizedUser;
    private Role $teknisiRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $this->teknisiRole = Role::where('slug', 'teknisi')->first();

        $this->superAdmin = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->attach($superAdminRole->id);

        $this->teknisiUser = User::create([
            'username' => 'teknisi1',
            'name' => 'Budi Teknisi',
            'email' => 'budi.teknisi@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->teknisiUser->roles()->attach($this->teknisiRole->id);

        $this->unauthorizedUser = User::create([
            'username' => 'guestuser',
            'name' => 'Guest User',
            'email' => 'guest@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_portal_teknisi(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('portal-teknisi.index'));

        $response->assertStatus(200);
        $response->assertSee('Portal Teknisi Lapangan');
    }

    public function test_teknisi_role_can_access_portal_teknisi(): void
    {
        $response = $this->actingAs($this->teknisiUser)->get(route('portal-teknisi.index'));

        $response->assertStatus(200);
        $response->assertSee('Portal Teknisi Lapangan');
        $response->assertSee('Budi Teknisi');
        $response->assertDontSee('Role: Teknisi');
    }

    public function test_unauthorized_user_cannot_access_portal_teknisi(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('portal-teknisi.index'));

        $response->assertStatus(403);
    }

    public function test_portal_teknisi_displays_assigned_work_orders(): void
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Uji WO',
            'category' => 'HOME',
            'service_number' => '9202300001',
            'created_by' => $this->superAdmin->id,
        ]);

        $wo = WorkOrder::create([
            'work_order_number' => 'WO-TEST-001',
            'customer_id' => $customer->id,
            'type' => 'INSTALASI_BARU',
            'status' => 'SCHEDULED',
            'assigned_to_id' => $this->teknisiUser->id,
            'scheduled_date' => now()->toDateString(),
            'created_by' => $this->superAdmin->id,
        ]);

        $response = $this->actingAs($this->teknisiUser)->get(route('portal-teknisi.index'));

        $response->assertStatus(200);
        $response->assertSee('WO-TEST-001');
        $response->assertSee('Pelanggan Uji WO');
        $response->assertSee('Instalasi Baru');
    }

    public function test_roles_page_lists_teknisi_role(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Teknisi');
        $response->assertSee(route('roles.edit', $this->teknisiRole));
    }

    public function test_can_manage_permissions_for_teknisi_role(): void
    {
        $ticketViewPerm = Permission::where('slug', 'ticket.view')->first();
        $this->assertNotNull($ticketViewPerm);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('roles.update', $this->teknisiRole), [
                'permission_ids' => [$ticketViewPerm->id],
            ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertTrue($this->teknisiRole->fresh()->permissions->contains('id', $ticketViewPerm->id));
    }
}
