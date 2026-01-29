<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TierUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'membership_tier_id',
        'discount_amount'
    ];
}