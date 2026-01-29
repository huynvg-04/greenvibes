<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $guard_name = 'web';
    
    protected $fillable = ['title', 'image', 'link', 'status'];
}
