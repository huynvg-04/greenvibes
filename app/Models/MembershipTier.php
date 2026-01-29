<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rank_priority',
        'min_spent',
        'min_orders',
        'discount',
        'usage_limit',
        'usage_period',
        'color_hex',
        'validity_days'
    ];
}
