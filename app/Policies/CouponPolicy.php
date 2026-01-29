<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('coupon.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('coupon.create');
    }

    public function update(User $user, Coupon $coupon)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('coupon.update');
    }

    public function delete(User $user, Coupon $coupon)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('coupon.delete');
    }
}