<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_item_id', 'rating', 'comment', 
        'status', 'images', 'likes_count'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function likes()
    {
        return $this->hasMany(ReviewLike::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            OrderItem::class,
            'id', 
            'id', 
            'order_item_id', 
            'product_id' 
        );
    }
}