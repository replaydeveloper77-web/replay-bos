<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseResourceTest extends TestCase
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

    public function test_superadmin_can_view_database_resources_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('database-resources.index'));

        $response->assertStatus(200);
        $response->assertSee('Resource Database');
        $response->assertSee('Ukuran Total Database');
        $response->assertSee('Koneksi Database Aktif');
        $response->assertSee('users');
    }

    public function test_unauthorized_user_cannot_view_database_resources(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('database-resources.index'));

        $response->assertStatus(403);
    }

    public function test_superadmin_can_optimize_single_table(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('database-resources.optimize'), [
            'table' => 'users',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_superadmin_can_optimize_all_tables(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('database-resources.optimize-all'));

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_superadmin_can_fetch_json_stats(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('database-resources.stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'database' => [
                    'name',
                    'version',
                    'total_bytes',
                    'total_formatted',
                    'total_tables',
                    'total_rows',
                    'threads_connected',
                ],
                'disk',
                'php',
            ],
        ]);
    }
}
