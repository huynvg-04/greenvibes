<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed test permissions and roles
        $this->seed(\Database\Seeders\TestPermissionSeeder::class);
    }

    public function test_manager_can_access_admin_dashboard()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_staff_can_access_admin_dashboard()
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_dashboard()
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_manager_can_access_product_create()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get(route('admin.products.create'));
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_product_create()
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('admin.products.create'));
        $response->assertStatus(403);
    }
}
