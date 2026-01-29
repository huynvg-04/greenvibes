<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\ShippingRate;

class CheckoutSettingSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'name' => 'Thanh toán khi nhận hàng (COD)',
            'code' => 'cod',
            'description' => 'Thanh toán tiền mặt cho nhăn viên giao hàng khi nhận hàng.',
            'is_active' => true,
            'sort_order' => 1
        ]);

        PaymentMethod::create([
            'name' => 'VNPAY',
            'code' => 'vnpay',
            'description' => 'Thanh toán qua ví điện tử VNPAY.',
            'is_active' => true,
            'sort_order' => 2
        ]);

        PaymentMethod::create([
            'name' => 'Momo',
            'code' => 'momo',
            'description' => 'Thanh toán qua ví điện tử Momo.',
            'is_active' => false,
            'sort_order' => 3
        ]);

        ShippingRate::create([
            'name' => 'Tiêu chuẩn',
            'fee' => 30000,
            'estimated_days' => 3,
            'is_active' => true
        ]);

        ShippingRate::create([
            'name' => 'Hỏa tốc',
            'fee' => 60000,
            'estimated_days' => 1,
            'is_active' => true
        ]);
        
        ShippingRate::create([
            'name' => 'Miễn phí vận chuyển',
            'fee' => 0,
            'min_order_value' => 450000,
            'estimated_days' => 4,
            'is_active' => true
        ]);
    }
}