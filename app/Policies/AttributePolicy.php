<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attribute;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttributePolicy
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
        return $user->hasPermissionTo('attribute.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('attribute.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('attribute.create');
    }

    public function update(User $user, Attribute $attribute)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('attribute.update');
    }

    public function delete(User $user, Attribute $attribute)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('attribute.delete');
    }
}
