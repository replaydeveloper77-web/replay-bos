<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndPermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $this->superAdmin = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->attach($superAdminRole->id);

        $this->unauthorizedUser = User::create([
            'username' => 'guestuser',
            'name' => 'Guest User',
            'email' => 'guest@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_view_roles_and_permissions_page_with_add_buttons(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Roles &amp; Permissions', false);
        $response->assertSee('Tambah Role');
        $response->assertSee('Tambah Permission');
        $response->assertSee('Daftar Role');
        $response->assertSee('Daftar Permission');
    }

    public function test_superadmin_can_create_a_new_role_without_permissions(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('roles.store'), [
            'name' => 'Quality Assurance',
            'slug' => 'qa',
            'description' => 'Memastikan kualitas software dan kestabilan sistem.',
        ]);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('roles', [
            'name' => 'Quality Assurance',
            'slug' => 'qa',
            'description' => 'Memastikan kualitas software dan kestabilan sistem.',
        ]);

        $role = Role::where('slug', 'qa')->first();
        $this->assertNotNull($role);
        $this->assertCount(0, $role->permissions);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE_ROLE',
            'target' => 'Role',
            'target_id' => $role->id,
        ]);
    }

    public function test_superadmin_can_create_a_new_role_with_auto_generated_slug_and_permissions(): void
    {
        $perm1 = Permission::where('slug', 'ticket.view')->first();
        $perm2 = Permission::where('slug', 'ticket.create')->first();

        $response = $this->actingAs($this->superAdmin)->post(route('roles.store'), [
            'name' => 'Network Leader',
            'slug' => '', // Auto generate slug
            'description' => 'Memimpin regu penanganan insiden jaringan.',
            'permission_ids' => [$perm1->id, $perm2->id],
        ]);

        $response->assertRedirect(route('roles.index'));

        $role = Role::where('name', 'Network Leader')->first();
        $this->assertNotNull($role);
        $this->assertEquals('network-leader', $role->slug);
        $this->assertTrue($role->permissions->contains('id', $perm1->id));
        $this->assertTrue($role->permissions->contains('id', $perm2->id));
    }

    public function test_superadmin_can_create_a_new_permission_and_assign_to_roles(): void
    {
        $roleSuper = Role::where('slug', 'super-admin')->first();
        $roleNoc = Role::where('slug', 'noc-admin')->first();

        $response = $this->actingAs($this->superAdmin)->post(route('permissions.store'), [
            'name' => 'Export Rekap Tiket Bulanan',
            'slug' => 'ticket.export_monthly',
            'module' => 'ticket',
            'description' => 'Hak akses untuk mendownload arsip tiket bulanan.',
            'role_ids' => [$roleSuper->id, $roleNoc->id],
        ]);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('permissions', [
            'name' => 'Export Rekap Tiket Bulanan',
            'slug' => 'ticket.export_monthly',
            'module' => 'ticket',
        ]);

        $permission = Permission::where('slug', 'ticket.export_monthly')->first();
        $this->assertNotNull($permission);
        $this->assertTrue($permission->roles->contains('id', $roleSuper->id));
        $this->assertTrue($permission->roles->contains('id', $roleNoc->id));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE_PERMISSION',
            'target' => 'Permission',
            'target_id' => $permission->id,
        ]);
    }

    public function test_unauthorized_user_cannot_create_role_or_permission(): void
    {
        $responseRole = $this->actingAs($this->unauthorizedUser)->post(route('roles.store'), [
            'name' => 'Hacker Role',
        ]);
        $responseRole->assertStatus(403);

        $responsePerm = $this->actingAs($this->unauthorizedUser)->post(route('permissions.store'), [
            'name' => 'Hacker Permission',
            'slug' => 'hacker.hack',
            'module' => 'system',
        ]);
        $responsePerm->assertStatus(403);
    }

    public function test_validation_fails_when_role_slug_is_already_taken(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('roles.store'), [
            'name' => 'Duplikat Super Admin',
            'slug' => 'super-admin', // Sudah ada
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_validation_fails_when_permission_fields_are_invalid(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('permissions.store'), [
            'name' => '',
            'slug' => 'ticket.view', // Sudah ada
            'module' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'slug', 'module']);
    }
}
