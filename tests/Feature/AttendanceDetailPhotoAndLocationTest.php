<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceDetailPhotoAndLocationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(RolePermissionSeeder::class);

        $this->user = User::create([
            'username' => 'karyawan1',
            'name' => 'Karyawan Replay',
            'email' => 'karyawan1@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->employee = Employee::create([
            'employee_number' => 'EMP-TEST-001',
            'name' => 'Karyawan Replay',
            'user_id' => $this->user->id,
            'position' => 'Developer',
            'department' => 'OTHER',
            'employment_status' => 'TETAP',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        config(['inertia.testing.ensure_pages_exist' => false]);
    }

    public function test_employee_can_check_in_with_base64_photo_and_location(): void
    {
        // 1x1 transparent PNG in base64
        $fakeBase64 = 'data:image/jpeg;base64,'.base64_encode('fake-jpeg-photo-content');

        $response = $this->actingAs($this->user)->post(route('attendance.check-in'), [
            'lat' => -6.2087634,
            'lng' => 106.845599,
            'address' => 'Jl. Jenderal Sudirman No. 1, Jakarta Pusat',
            'photo' => $fakeBase64,
            'notes' => 'Hadir tepat waktu di kantor pusat',
        ]);

        $response->assertSessionHas('status', 'Check-in berhasil dicatat.');

        $attendance = Attendance::where('employee_id', $this->employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->check_in_at);
        $this->assertEquals(-6.2087634, (float) $attendance->check_in_lat);
        $this->assertEquals(106.845599, (float) $attendance->check_in_lng);
        $this->assertEquals('Jl. Jenderal Sudirman No. 1, Jakarta Pusat', $attendance->check_in_address);
        $this->assertNotNull($attendance->check_in_photo_path);

        Storage::disk('public')->assertExists($attendance->check_in_photo_path);
        $this->assertEquals('fake-jpeg-photo-content', Storage::disk('public')->get($attendance->check_in_photo_path));
    }

    public function test_employee_can_check_out_with_base64_photo_and_location(): void
    {
        // First check in
        $attendance = Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => now()->toDateString(),
            'check_in_at' => now()->subHours(8),
            'check_in_lat' => -6.2087,
            'check_in_lng' => 106.8455,
            'check_in_address' => 'Kantor Replay',
        ]);

        $fakeBase64Out = 'data:image/jpeg;base64,'.base64_encode('fake-checkout-jpeg-photo');

        $response = $this->actingAs($this->user)->post(route('attendance.check-out'), [
            'lat' => -6.2090,
            'lng' => 106.8460,
            'address' => 'Jl. Gatot Subroto No. 45, Jakarta Selatan',
            'photo' => $fakeBase64Out,
            'notes' => 'Selesai tugas hari ini',
        ]);

        $response->assertSessionHas('status', 'Check-out berhasil dicatat.');

        $attendance->refresh();

        $this->assertNotNull($attendance->check_out_at);
        $this->assertEquals(-6.2090, (float) $attendance->check_out_lat);
        $this->assertEquals(106.8460, (float) $attendance->check_out_lng);
        $this->assertEquals('Jl. Gatot Subroto No. 45, Jakarta Selatan', $attendance->check_out_address);
        $this->assertNotNull($attendance->check_out_photo_path);

        Storage::disk('public')->assertExists($attendance->check_out_photo_path);
        $this->assertEquals('fake-checkout-jpeg-photo', Storage::disk('public')->get($attendance->check_out_photo_path));
    }

    public function test_portal_karyawan_props_include_photo_and_location_details(): void
    {
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => now()->toDateString(),
            'check_in_at' => now()->subHours(8),
            'check_in_lat' => -6.2087,
            'check_in_lng' => 106.8455,
            'check_in_address' => 'Kantor Replay Jakarta',
            'check_in_photo_path' => 'attendances/test/checkin.jpg',
            'check_out_at' => now(),
            'check_out_lat' => -6.2090,
            'check_out_lng' => 106.8460,
            'check_out_address' => 'Lobi Gedung Replay',
            'check_out_photo_path' => 'attendances/test/checkout.jpg',
        ]);

        $response = $this->actingAs($this->user)->get(route('portal-karyawan.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('PortalKaryawan/Index')
            ->has('today.check_in_photo_url')
            ->where('today.check_in_address', 'Kantor Replay Jakarta')
            ->has('today.check_out_photo_url')
            ->where('today.check_out_address', 'Lobi Gedung Replay')
        );
    }
}
