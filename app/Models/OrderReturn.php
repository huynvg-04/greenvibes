<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'reason', 'description', 'images', 
        'status', 'refund_amount', 'admin_note', 'processed_by'
    ];

    protected $casts = [
        'images' => 'array', 
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function items() {
        return $this->hasMany(OrderReturnItem::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Dùng order code (ORD-XXXXXX) làm route key thay vì id số nguyên.
     */
    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    /**
     * Resolve route binding theo order.code (join qua bảng orders).
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        return static::whereHas('order', fn($q) => $q->where('code', $value))->first();
    }
}