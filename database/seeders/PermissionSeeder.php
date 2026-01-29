<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define permissions by resource
        $permissions = [
            // Products
            'product.view',
            'product.create',
            'product.update',
            'product.delete',

            // Categories
            'category.view',
            'category.create',
            'category.update',
            'category.delete',

            // Promotions
            'promotion.view',
            'promotion.create',
            'promotion.update',
            'promotion.delete',

            // Orders
            'order.view',
            'order.update',
            'order.viewAny',

            // Users (customers management)
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Banners
            'banner.view',
            'banner.create',
            'banner.update',
            'banner.delete',

            // Reviews
            'review.view',
            'review.delete',

            // Cart / Checkout
            'cart.view',
            'cart.checkout',


        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

     
        $manager->syncPermissions($permissions);

        // Staff: day-to-day admin tasks but not full management
        $staffPermissions = [
            // products: view, update
            'product.view', 'product.update',
            // categories: view, update
            'category.view', 'category.update',
            // promotions: view, update
            'promotion.view', 'promotion.update',
            // orders: view, update
            'order.view', 'order.update', 'order.viewAny',
            // banners: view, update
            'banner.view', 'banner.update',
            // reviews: view, delete
            'review.view', 'review.delete',
        ];
        $staff->syncPermissions($staffPermissions);

        // Customer: product view, cart/checkout, review view/create
        $customerPermissions = [
            'product.view',
            'cart.view', 'cart.checkout',
            'review.view',
        ];
        $customer->syncPermissions($customerPermissions);

        $this->command->info('Permissions and role assignments seeded.');
    }
}

