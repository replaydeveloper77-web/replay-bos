<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class CustomerImportTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->adminUser = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'admin@replay.net.id',
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $this->adminUser->roles()->attach($superAdminRole->id);
        }
    }

    public function test_can_download_excel_template(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('customers.import.template', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=template_import_pelanggan.xlsx');
    }

    public function test_can_download_csv_template(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('customers.import.template', ['format' => 'csv']));

        $response->assertStatus(200);
        $this->assertStringContainsString('Nama Pelanggan', $response->getContent());
        $this->assertStringContainsString('Nomor Layanan', $response->getContent());
    }

    public function test_import_csv_auto_generates_service_number(): void
    {
        $csvContent = implode("\n", [
            'Nama Pelanggan,Nama Perusahaan,Nama PIC,No WhatsApp,Email,Lokasi / Alamat,Layanan',
            'PT Maju Bersama,PT Maju Bersama,Budi Santoso,081234567890,budi@majubersama.com,"Jl. Sudirman No. 1, Jakarta",Internet Dedicated 100 Mbps',
            'Siti Aminah,,Siti Aminah,085712345678,siti@gmail.com,"Jl. Merdeka No. 10, Bandung",Broadband Home 50 Mbps',
        ]);

        $file = UploadedFile::fake()->createWithContent('customers.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post(route('customers.import'), [
                'file' => $file,
                'default_category' => 'HOME',
            ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseCount('customers', 2);

        $c1 = Customer::where('name', 'PT Maju Bersama')->first();
        $this->assertNotNull($c1);
        $this->assertEquals('PT Maju Bersama', $c1->company);
        $this->assertEquals('Budi Santoso', $c1->pic_name);
        $this->assertEquals('081234567890', $c1->whatsapp);
        $this->assertEquals('budi@majubersama.com', $c1->email);
        $this->assertEquals('Jl. Sudirman No. 1, Jakarta', $c1->location);
        $this->assertEquals('Internet Dedicated 100 Mbps', $c1->service);
        $this->assertEquals('9202300001', $c1->service_number);

        $c2 = Customer::where('name', 'Siti Aminah')->first();
        $this->assertNotNull($c2);
        $this->assertEquals('9202300002', $c2->service_number);
    }

    public function test_import_xlsx_with_mixed_categories_and_auto_service_numbers(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: Headers
        $sheet->fromArray([
            'Nama Pelanggan',
            'Nama Perusahaan',
            'Nama PIC',
            'No WhatsApp',
            'Email',
            'Lokasi / Alamat',
            'Layanan',
            'Kategori',
            'Nomor Layanan',
        ], null, 'A1');

        // Row 2: Corporate customer
        $sheet->fromArray([
            'PT Sinar Terang',
            'PT Sinar Terang',
            'Hendra',
            '081122334455',
            'hendra@sinarterang.com',
            'Kawasan Industri MM2100',
            'Dedicated 200 Mbps',
            'CORPORATE',
            '', // blank -> should generate 9202200001
        ], null, 'A2');

        // Row 3: Reseller customer
        $sheet->fromArray([
            'CV Global Net',
            'CV Global Net',
            'Dewi',
            '081987654321',
            'dewi@globalnet.id',
            'Jl. Pemuda No. 8',
            'Transit 1 Gbps',
            'RESELLER',
            '', // blank -> should generate 9202100001
        ], null, 'A3');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new UploadedFile($tempPath, 'pelanggan.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->adminUser)
            ->post(route('customers.import'), [
                'file' => $file,
                'default_category' => 'HOME',
            ]);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('status');

        $corporate = Customer::where('name', 'PT Sinar Terang')->first();
        $this->assertNotNull($corporate);
        $this->assertEquals('CORPORATE', $corporate->category);
        $this->assertEquals('9202200001', $corporate->service_number);

        $reseller = Customer::where('name', 'CV Global Net')->first();
        $this->assertNotNull($reseller);
        $this->assertEquals('RESELLER', $reseller->category);
        $this->assertEquals('9202100001', $reseller->service_number);
    }

    public function test_import_skips_empty_rows_and_validates_required_name(): void
    {
        $csvContent = implode("\n", [
            'Nama Pelanggan,Nama Perusahaan,Nama PIC,No WhatsApp,Email,Lokasi / Alamat,Layanan',
            'Pelanggan Valid,,,08123456789,,,',
            ',Perusahaan Tanpa Nama,,,,,,',
            ',,,,,,', // completely empty row
        ]);

        $file = UploadedFile::fake()->createWithContent('customers_invalid.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post(route('customers.import'), [
                'file' => $file,
                'default_category' => 'HOME',
            ]);

        $response->assertRedirect(route('customers.index'));

        // Only 1 customer should be created
        $this->assertDatabaseCount('customers', 1);
        $customer = Customer::first();
        $this->assertEquals('Pelanggan Valid', $customer->name);
        $this->assertEquals('9202300001', $customer->service_number);
    }

    public function test_import_normalizes_phone_numbers_and_synonym_headers(): void
    {
        $csvContent = implode("\n", [
            'Nama,Perusahaan,PIC,WhatsApp,Email,Alamat,Paket',
            'Paket Hebat,PT Maju Jaya,Pak Joko,81299887766,joko@majujaya.com,Jl. Thamrin No 5,50 Mbps',
            'Paket Super,,Santi,+62 878-1122-3344,santi@example.com,Jl. Melati No 12,20 Mbps',
        ]);

        $file = UploadedFile::fake()->createWithContent('customers_synonyms.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post(route('customers.import'), [
                'file' => $file,
                'default_category' => 'HOME',
            ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseCount('customers', 2);

        $c1 = Customer::where('name', 'Paket Hebat')->first();
        $this->assertEquals('081299887766', $c1->whatsapp);
        $this->assertEquals('9202300001', $c1->service_number);

        $c2 = Customer::where('name', 'Paket Super')->first();
        $this->assertEquals('6287811223344', $c2->whatsapp);
        $this->assertEquals('9202300002', $c2->service_number);
    }

    public function test_unauthorized_user_cannot_import_or_download_template(): void
    {
        $guestUser = User::create([
            'username' => 'guest_user',
            'name' => 'Guest User',
            'email' => 'guest@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $responseDownload = $this->actingAs($guestUser)
            ->get(route('customers.import.template'));
        $responseDownload->assertStatus(403);

        $file = UploadedFile::fake()->create('customers.csv', 100);
        $responseImport = $this->actingAs($guestUser)
            ->post(route('customers.import'), ['file' => $file]);
        $responseImport->assertStatus(403);
    }
}

