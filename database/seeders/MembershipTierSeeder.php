<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTierSeeder extends Seeder
{
    public function run()
    {
        DB::table('membership_tiers')->insert([
            [
                'name' => 'Bronze',
                'rank_priority' => 1,
                'min_spent' => 0,
                'min_orders' => 0,
                'color_hex' => '#CD7F32', 
                'validity_days' => null,  
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Silver',
                'rank_priority' => 2,
                'min_spent' => 1000000,
                'min_orders' => 5,
                'color_hex' => '#C0C0C0', 
                'validity_days' => 60,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Gold',
                'rank_priority' => 3,
                'min_spent' => 30000000, 
                'min_orders' => 10,
                'color_hex' => '#FFD700', 
                'validity_days' => 60,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Platinum',
                'rank_priority' => 4,
                'min_spent' => 10000000, 
                'min_orders' => 20,
                'color_hex' => '#E5E4E2',
                'validity_days' => 60,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Diamond',
                'rank_priority' => 5,
                'min_spent' => 40000000, 
                'min_orders' => 50,
                'color_hex' => '#b9f2ff', 
                'validity_days' => 60,
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}