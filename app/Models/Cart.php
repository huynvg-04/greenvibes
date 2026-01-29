<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'product_variant_id', 'quantity', 'is_selected'];

    protected static function booted()
    {
        static::creating(function ($cart) {
            if (is_null($cart->quantity) || $cart->quantity < 1) $cart->quantity = 1;
            $cart->is_selected = $cart->is_selected ?? true;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
