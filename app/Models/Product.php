<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string|null $embedding  
 * @property string $name
 * @property string $description
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'slug', 'description', 'discount_percent', 'status', 'category_id', 'sold_count'];


    public function completedOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id')
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            });
    }

    public function getImageUrlAttribute()
    {
        $primaryImage = $this->images->where('is_primary', 1)->first();
        if (!$primaryImage) {
            $primaryImage = $this->images->first();
        }
        if ($primaryImage) {
            $path = $primaryImage->image_url; 

            if (str_starts_with($path, 'http')) {
                return $path;
            }
            return asset('storage/' . $path);
        }

        return null;
    }

    protected static function booted()
    {
        static::updated(function ($product) {
  
            if ($product->isDirty('discount_percent')) {

                $variants = $product->variants;

                foreach ($variants as $variant) {
                    $percent = $product->discount_percent;
                    
                    if ($percent > 0 && $variant->list_price > 0) {
                        $discounted = $variant->list_price * ((100 - $percent) / 100);
                        $variant->sale_price = ceil($discounted / 1000) * 1000;
                    } else {
                        $variant->sale_price = $variant->list_price;
                    }

                    $variant->saveQuietly(); 
                }
            }
        });

        static::addGlobalScope('completed_sum', function ($builder) {
            $builder->withSum(
                ['completedOrderItems as completed_order_items_sum_quantity'],
                'quantity'
            );
        });
    }

    protected $guarded = [];
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function coupons()
    {
        return $this->morphToMany(Coupon::class, 'couponable');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'product_id',
            'order_item_id',
            'id',
            'id'
        );
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName()
    {
        if (request()->is('admin/*')) {
            return 'id';
        }
        return 'slug';
    }
}