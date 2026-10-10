<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketProgressUpdate;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketProgressUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->adminUser = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $role = Role::where('slug', 'super-admin')->first();
        if ($role) {
            $this->adminUser->roles()->attach($role->id);
        }

        $this->ticket = Ticket::create([
            'ticket_number' => 'RS-20261008-0001',
            'title' => 'Gangguan Koneksi PT Leo Network',
            'description' => 'Koneksi internet putus sejak pagi.',
            'category' => 'RESELLER',
            'priority' => 'HIGH',
            'status' => 'OPEN',
            'reporter_id' => $this->adminUser->id,
            'public_token' => 'test-token-leo-network-12345',
            'created_by' => $this->adminUser->id,
        ]);
    }

    public function test_can_add_progress_update_with_photos(): void
    {
        Storage::fake('public');

        $photo1 = UploadedFile::fake()->image('cek_kabel.jpg', 600, 400);
        $photo2 = UploadedFile::fake()->image('splicing.jpg', 600, 400);

        $response = $this->actingAs($this->adminUser)
            ->post(route('tickets.progress-updates.store', $this->ticket), [
                'title' => 'Pengecekan Kabel FO',
                'note' => 'Teknisi tiba di tiang odp-04, ditemukan kabel bending parah.',
                'status' => 'HANDLE',
                'photos' => [$photo1, $photo2],
            ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('ticket_progress_updates', [
            'ticket_id' => $this->ticket->id,
            'title' => 'Pengecekan Kabel FO',
            'note' => 'Teknisi tiba di tiang odp-04, ditemukan kabel bending parah.',
            'status' => 'HANDLE',
        ]);

        $update = TicketProgressUpdate::where('ticket_id', $this->ticket->id)->first();
        $this->assertNotNull($update);
        $this->assertCount(2, $update->attachments);

        // Verify ticket status was updated to HANDLE
        $this->ticket->refresh();
        $this->assertEquals('HANDLE', $this->ticket->status);
    }

    public function test_timeline_contains_progress_update(): void
    {
        $update = TicketProgressUpdate::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->adminUser->id,
            'title' => 'Penggantian Patchcord',
            'note' => 'Patchcord baru sudah terpasang, redaman normal -18 dBm.',
            'status' => 'HANDLE',
        ]);

        $timeline = $this->ticket->timeline();
        $progressEvents = $timeline->where('type', 'PROGRESS_UPDATE');

        $this->assertCount(1, $progressEvents);
        $this->assertEquals('Penggantian Patchcord', $progressEvents->first()['title']);
    }

    public function test_public_ticket_page_shows_smartphone_mockup_with_progress_and_photos(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('hasil_perbaikan.jpg', 600, 400);

        $this->actingAs($this->adminUser)
            ->post(route('tickets.progress-updates.store', $this->ticket), [
                'title' => 'Splicing Selesai',
                'note' => 'Penyambungan core selesai dilakukan.',
                'status' => 'RESOLVED',
                'photos' => [$photo],
            ]);

        $response = $this->get(route('public.ticket', ['token' => $this->ticket->public_token]));

        $response->assertStatus(200);
        // Verify phone mockup classes exist
        $response->assertSee('phone-device', false);
        $response->assertSee('dynamic-island', false);
        $response->assertSee('phone-status-bar', false);

        // Verify ticket details & progress update content exist
        $response->assertSee($this->ticket->ticket_number);
        $response->assertSee('Splicing Selesai');
        $response->assertSee('Penyambungan core selesai dilakukan.');
        $response->assertSee('hasil_perbaikan.jpg');
    }

    public function test_can_delete_progress_update(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('test_delete.jpg', 600, 400);

        $this->actingAs($this->adminUser)
            ->post(route('tickets.progress-updates.store', $this->ticket), [
                'title' => 'Update Sementara',
                'note' => 'Catatan sementara.',
                'photos' => [$photo],
            ]);

        $update = TicketProgressUpdate::where('ticket_id', $this->ticket->id)->first();
        $this->assertNotNull($update);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('tickets.progress-updates.destroy', [$this->ticket, $update]));

        $response->assertRedirect(route('tickets.show', $this->ticket));
        $this->assertDatabaseMissing('ticket_progress_updates', ['id' => $update->id]);
    }

    public function test_public_tracking_link_does_not_expire_when_ticket_is_closed_and_displays_rfo(): void
    {
        // 1. Resolve ticket with RFO note
        $this->actingAs($this->adminUser)
            ->post(route('tickets.status', $this->ticket), [
                'status' => 'RESOLVED',
                'note' => 'Penyebab: FO Cut akibat galian PDAM di KM 12. Tindakan: Splicing core 1-4 dan proteksi tray baru.',
            ]);

        $this->ticket->refresh();
        $this->assertFalse($this->ticket->isPublicLinkExpired());

        $responseResolved = $this->get(route('public.ticket', ['token' => $this->ticket->public_token]));
        $responseResolved->assertStatus(200);
        $responseResolved->assertSee('Laporan RFO (Reason For Outage)', false);
        $responseResolved->assertSee('Penyebab: FO Cut akibat galian PDAM di KM 12', false);
        $responseResolved->assertSee('Durasi Gangguan', false);
        $responseResolved->assertSee('SOLVED', false);

        // 2. Close ticket
        $this->actingAs($this->adminUser)
            ->post(route('tickets.status', $this->ticket), [
                'status' => 'CLOSED',
                'note' => 'Tiket ditutup, layanan pelanggan telah dipastikan aktif normal.',
            ]);

        $this->ticket->refresh();
        $this->assertEquals('CLOSED', $this->ticket->status);
        $this->assertFalse($this->ticket->isPublicLinkExpired());

        // 3. Link should still be accessible (200, not 410)
        $responseClosed = $this->get(route('public.ticket', ['token' => $this->ticket->public_token]));
        $responseClosed->assertStatus(200);
        $responseClosed->assertSee('Laporan RFO (Reason For Outage)', false);
        $responseClosed->assertSee('CLOSED', false);
        $responseClosed->assertSee('permanen &amp; tidak kadaluarsa', false);
    }

    public function test_whatsapp_summary_contains_rfo_and_tracking_link_for_closed_ticket(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('tickets.status', $this->ticket), [
                'status' => 'RESOLVED',
                'note' => 'RFO: Adaptor modem rusak tersambar petir. Diganti dengan unit baru ZTE F670L.',
            ]);

        $this->ticket->refresh();
        $summary = $this->ticket->whatsappSummary();

        $this->assertStringContainsString('*RFO (Reason For Outage):*', $summary);
        $this->assertStringContainsString('Adaptor modem rusak tersambar petir', $summary);
        $this->assertStringContainsString($this->ticket->publicUrl(), $summary);
    }

    public function test_can_update_resolution_note_rfo_via_ticket_edit(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('tickets.update', $this->ticket), [
                'title' => 'Tiket dengan RFO terupdate',
                'description' => 'Deskripsi gangguan lengkap',
                'category' => 'LINK_DOWN',
                'priority' => 'HIGH',
                'resolution_note' => 'RFO Baru: Pembersihan konektor patchcord di OTB.',
            ]);

        $response->assertRedirect(route('tickets.show', $this->ticket));
        $this->ticket->refresh();
        $this->assertEquals('RFO Baru: Pembersihan konektor patchcord di OTB.', $this->ticket->resolution_note);
    }

    public function test_long_unbroken_text_in_progress_update_has_anti_overflow_classes_and_styles(): void
    {
        $longString = str_repeat('!', 100);

        $this->actingAs($this->adminUser)
            ->post(route('tickets.progress-updates.store', $this->ticket), [
                'title' => 'Cek Kabel',
                'note' => 'Penanganan: '.$longString,
                'status' => 'HANDLE',
            ]);

        $response = $this->get(route('public.ticket', ['token' => $this->ticket->public_token]));
        $response->assertStatus(200);
        $response->assertSee('overflow-wrap: anywhere', false);
        $response->assertSee('overflow-x: hidden', false);
        $response->assertSee($longString);
    }
}
