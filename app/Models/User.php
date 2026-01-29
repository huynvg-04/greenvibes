<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'email_verified_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        if (method_exists($this, 'hasAnyRole')) {
            return $this->hasAnyRole(['manager', 'staff', 'admin']);
        }

        return isset($this->role) && $this->role === 'admin';
    }

    public function isCustomer()
    {
        if (method_exists($this, 'hasRole')) {
            return $this->hasRole('customer');
        }

        return isset($this->role) && $this->role === 'customer';
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function hasInWishlist($productId)
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }
}
