<?php

namespace App\Policies;

use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingRatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
   public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('setting.view'); 
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ShippingRate $shippingRate)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('setting.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ShippingRate $shippingRate)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('setting.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ShippingRate $shippingRate)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('setting.delete');
    }
    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ShippingRate $shippingRate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ShippingRate $shippingRate)
    {
        //
    }
}
