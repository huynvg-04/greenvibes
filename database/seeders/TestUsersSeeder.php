<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ensure roles exist
        $roles = ['customer', 'staff', 'manager'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
            ]
        );
        $manager->syncRoles(['manager']);

        // Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );
        $staff->syncRoles(['staff']);

        // Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
            ]
        );
        $customer->syncRoles(['customer']);

        $this->command->info('Test users created/ensured: manager@example.com, staff@example.com, customer@example.com (password: password)');
    }
}
