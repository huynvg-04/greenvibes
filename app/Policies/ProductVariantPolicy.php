<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductVariantPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_variant.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_variant.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_variant.create');
    }

    public function update(User $user, ProductVariant $productVariant)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_variant.update');
    }

    public function delete(User $user, ProductVariant $productVariant)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('product_variant.delete');
    }
}
