<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'image',
        'stock',
        'standard_cost',
        'list_price',
        'sale_price',
        'sold_count'
    ];
    protected $guarded = [];
    protected $touches = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function coupons()
    {
        return $this->morphToMany(Coupon::class, 'couponable');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'attribute_value_id'
        );
    }
}
