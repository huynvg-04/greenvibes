<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'is_published',
        'user_id',
        'category_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'blog_likes');
    }


    public function isLikedByAuthUser()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->likes()->where('user_id', Auth::id())->exists();
    }

    public function getRouteKeyName()
    {
        $request = request();
        if ($request && $request->is('admin/*')) {
            return 'id';
        }
        return 'slug';
    }
}
