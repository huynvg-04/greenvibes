<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UpdatePermissionNamesSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'coupon.view'   => ['name' => 'Xem mã giảm giá', 'group' => 'Mã giảm giá'],
            'coupon.create' => ['name' => 'Thêm mã giảm giá', 'group' => 'Mã giảm giá'],
            'coupon.update' => ['name' => 'Sửa mã giảm giá', 'group' => 'Mã giảm giá'],
            'coupon.delete' => ['name' => 'Xóa mã giảm giá', 'group' => 'Mã giảm giá'],

            'product.view'   => ['name' => 'Xem sản phẩm', 'group' => 'Sản phẩm'],
            'product.create' => ['name' => 'Thêm sản phẩm', 'group' => 'Sản phẩm'],
            'product.update' => ['name' => 'Sửa sản phẩm', 'group' => 'Sản phẩm'],
            'product.delete' => ['name' => 'Xóa sản phẩm', 'group' => 'Sản phẩm'],

            'category.view'   => ['name' => 'Xem danh mục', 'group' => 'Danh mục'],
            'category.create' => ['name' => 'Thêm danh mục', 'group' => 'Danh mục'],
        ];

        foreach ($permissions as $code => $info) {
            Permission::where('name', $code)->update([
                'display_name' => $info['name'],
                'group_name' => $info['group']
            ]);
        }
    }
}
