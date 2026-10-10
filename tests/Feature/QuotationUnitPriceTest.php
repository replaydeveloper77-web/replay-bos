<?php

namespace Tests\Feature;

use App\Models\Quotation;
use App\Models\Role;
use App\Models\ServicePackage;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationUnitPriceTest extends TestCase
{
    use RefreshDatabase;

    private User $marketingUser;
    private ServicePackage $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $marketingRole = Role::where('slug', 'marketing')->first();

        $this->marketingUser = User::create([
            'username' => 'marketing',
            'name' => 'Marketing Staff',
            'email' => 'marketing@replay.net.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->marketingUser->roles()->attach($marketingRole->id);

        $this->package = ServicePackage::create([
            'name' => 'Dedicated 300 Mbps',
            'code' => 'DED-300M',
            'category' => 'CORPORATE',
            'speed_mbps' => 300,
            'price' => 3000000,
            'is_active' => true,
            'created_by' => $this->marketingUser->id,
        ]);
    }

    public function test_quotation_create_page_contains_readonly_price_and_format_nominal(): void
    {
        $response = $this->actingAs($this->marketingUser)->get(route('quotations.create'));

        $response->assertStatus(200);
        $response->assertSee('item-price-display');
        $response->assertSee('formatNominal');
        $response->assertSee('readonly');
    }

    public function test_can_submit_quotation_with_formatted_price_containing_dots(): void
    {
        $response = $this->actingAs($this->marketingUser)->post(route('quotations.store'), [
            'prospect_name' => 'PT Harapan Bangsa',
            'prospect_company' => 'Harapan Bangsa Corp',
            'prospect_phone' => '081234567890',
            'items' => [
                [
                    'service_package_id' => $this->package->id,
                    'item_name' => 'Dedicated 300 Mbps (300 Mbps)',
                    'qty' => 1,
                    'unit_price' => '3.000.000', // String formatted with thousand dots
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'prospect_name' => 'PT Harapan Bangsa',
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'item_name' => 'Dedicated 300 Mbps (300 Mbps)',
            'unit_price' => 3000000,
            'qty' => 1,
        ]);
    }

    public function test_can_submit_quotation_with_raw_numeric_price(): void
    {
        $response = $this->actingAs($this->marketingUser)->post(route('quotations.store'), [
            'prospect_name' => 'CV Maju Jaya',
            'prospect_phone' => '081987654321',
            'items' => [
                [
                    'service_package_id' => $this->package->id,
                    'item_name' => 'Dedicated 300 Mbps',
                    'qty' => 2,
                    'unit_price' => 3000000,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('quotation_items', [
            'item_name' => 'Dedicated 300 Mbps',
            'unit_price' => 3000000,
            'qty' => 2,
        ]);
    }

    public function test_can_update_quotation_with_formatted_price(): void
    {
        $quotation = Quotation::create([
            'quotation_number' => 'QUO-202610-0001',
            'prospect_name' => 'Client Awal',
            'subtotal' => 3000000,
            'ppn_rate' => 11,
            'ppn_amount' => 330000,
            'total' => 3330000,
            'status' => 'DRAFT',
            'created_by' => $this->marketingUser->id,
        ]);

        $response = $this->actingAs($this->marketingUser)->put(route('quotations.update', $quotation), [
            'prospect_name' => 'Client Diperbarui',
            'items' => [
                [
                    'service_package_id' => $this->package->id,
                    'item_name' => 'Dedicated 300 Mbps',
                    'qty' => 1,
                    'unit_price' => '3.000.000',
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'prospect_name' => 'Client Diperbarui',
        ]);
    }
}
