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
}