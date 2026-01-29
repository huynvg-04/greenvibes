<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('banner.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('banner.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('banner.create');
    }

    public function update(User $user, Banner $banner)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('banner.update');
    }

    public function delete(User $user, Banner $banner)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('banner.delete');
    }
}