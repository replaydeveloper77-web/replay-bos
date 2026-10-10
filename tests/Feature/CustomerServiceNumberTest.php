<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceNumberTest extends TestCase
{
    use RefreshDatabase;

    private CustomerService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CustomerService::class);
        $this->user = User::create([
            'username' => 'testadmin',
            'name' => 'Test Admin',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_service_number_format_for_reseller(): void
    {
        $num1 = $this->service->generateServiceNumber('RESELLER');
        $this->assertEquals('9202100001', $num1);

        Customer::create([
            'name' => 'Reseller 1',
            'category' => 'RESELLER',
            'service_number' => $num1,
            'created_by' => $this->user->id,
        ]);

        $num2 = $this->service->generateServiceNumber('RESELLER');
        $this->assertEquals('9202100002', $num2);
    }

    public function test_service_number_format_for_corporate(): void
    {
        $num1 = $this->service->generateServiceNumber('CORPORATE');
        $this->assertEquals('9202200001', $num1);

        Customer::create([
            'name' => 'Corp 1',
            'category' => 'CORPORATE',
            'service_number' => $num1,
            'created_by' => $this->user->id,
        ]);

        $num2 = $this->service->generateServiceNumber('CORPORATE');
        $this->assertEquals('9202200002', $num2);
    }

    public function test_service_number_format_for_home(): void
    {
        $num1 = $this->service->generateServiceNumber('HOME');
        $this->assertEquals('9202300001', $num1);

        Customer::create([
            'name' => 'Home 1',
            'category' => 'HOME',
            'service_number' => $num1,
            'created_by' => $this->user->id,
        ]);

        $num2 = $this->service->generateServiceNumber('HOME');
        $this->assertEquals('9202300002', $num2);
    }

    public function test_categories_maintain_independent_sequences(): void
    {
        $reseller = Customer::create([
            'name' => 'Reseller Alpha',
            'category' => 'RESELLER',
            'service_number' => $this->service->generateServiceNumber('RESELLER'),
            'created_by' => $this->user->id,
        ]);

        $corporate = Customer::create([
            'name' => 'Corporate Beta',
            'category' => 'CORPORATE',
            'service_number' => $this->service->generateServiceNumber('CORPORATE'),
            'created_by' => $this->user->id,
        ]);

        $home = Customer::create([
            'name' => 'Home Gamma',
            'category' => 'HOME',
            'service_number' => $this->service->generateServiceNumber('HOME'),
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('9202100001', $reseller->service_number);
        $this->assertEquals('9202200001', $corporate->service_number);
        $this->assertEquals('9202300001', $home->service_number);
    }

    public function test_customer_service_create_auto_assigns_service_number(): void
    {
        $c = $this->service->create([
            'name' => 'Pelanggan Baru',
            'category' => 'HOME',
        ], $this->user, new \Illuminate\Http\Request());

        $this->assertEquals('9202300001', $c->service_number);
    }

    public function test_service_number_preserves_0000_for_two_digit_sequence(): void
    {
        Customer::create([
            'name' => 'Reseller 9',
            'category' => 'RESELLER',
            'service_number' => '9202100009',
            'created_by' => $this->user->id,
        ]);

        $num10 = $this->service->generateServiceNumber('RESELLER');
        $this->assertEquals('92021000010', $num10);
    }
}
