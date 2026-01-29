<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'type',             
        'quantity',         
        'balance_after',   
        'reference_type', 
        'reference_id',   
        'user_id',
        'description'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}