<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;

        try {
            return $user->hasPermissionTo('product.view');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false; 
        }
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product.show');
    }

    public function create(User $user)
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('manager')) {
            return true;
        }

        try {
            return $user->hasPermissionTo('product.create');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function update(User $user, Product $product)
    {
        return $user->hasPermissionTo('product.update');
    }

    public function delete(User $user, Product $product)
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('manager')) {
            return true;
        }

        try {
            return $user->hasPermissionTo('product.delete');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }
}
