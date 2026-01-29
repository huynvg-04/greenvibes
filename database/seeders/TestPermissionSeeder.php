<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestPermissionSeeder extends Seeder
{
    public function run()
    {
        // Minimal permissions used by policies in tests
        $permissions = [
            'product.view', 'product.create', 'product.update', 'product.delete',
            'category.view', 'category.create', 'category.update', 'category.delete',
            'order.view', 'order.create', 'order.update',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $manager = Role::firstOrCreate(['name' => 'manager']);

        // Assign a small set of permissions
        $customer->syncPermissions(['product.view', 'order.create', 'order.view']);
        $staff->syncPermissions(['product.view', 'order.view', 'order.update']);
        $manager->syncPermissions(Permission::pluck('name')->toArray());
    }
}
