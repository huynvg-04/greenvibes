<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductImagePolicy
{
    use HandlesAuthorization;

     public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_image.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_image.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_image.create');
    }

    public function update(User $user, $image = null)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_image.update');
    }

    public function delete(User $user, $image = null)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_image.delete');
    }
}
