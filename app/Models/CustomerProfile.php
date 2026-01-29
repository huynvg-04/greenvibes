<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_tier_id',
        'full_name',
        'gender',
        'phone',
        'address',
        'facebook_id',
        'google_id',
        'status',
        'level_updated_at',
        'level_expires_at',
        'total_spent_lifetime',
        'total_orders_lifetime',
    ];

    protected $casts = [
        'level_updated_at' => 'datetime',
        'level_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }
    
    public function getLevelNameAttribute()
    {
        return $this->membershipTier ? $this->membershipTier->name : 'Thành viên mới';
    }
}
