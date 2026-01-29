<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'name',
        'fee',
        'min_order_value',
        'estimated_days',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee' => 'float',
        'min_order_value' => 'float',
    ];
}
