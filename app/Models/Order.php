<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Symfony\Component\Translation\t;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'shipping_address', 'total_amount', 'shipping_fee', 'status', 'code', 'phone', 'note', 'payment_method', 'shipping_method', 'discount_amount', 'coupon_discount', 'tier_discount', 'coupon_code', 'order_date'];

    protected $casts = ['order_date' => 'datetime'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method', 'code');
    }

    public function returns()
    {
        return $this->hasOne(OrderReturn::class);
    }

    public function getRouteKeyName()
    {
        if (request()->is('admin/*')) {
            return 'id';
        }
        return 'code';
    }
}
