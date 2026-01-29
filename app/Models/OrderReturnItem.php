<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturnItem extends Model
{
    protected $fillable = ['order_return_id', 'order_item_id', 'quantity'];

    public function orderItem() {
        return $this->belongsTo(OrderItem::class);
    }
}