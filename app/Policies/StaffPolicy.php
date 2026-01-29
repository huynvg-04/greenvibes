<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
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
        return $user->hasPermissionTo('staff.view');
    }

    public function view(User $user, User $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('staff.view')) {
            if ($model->hasRole('manager') && $user->id !== $model->id) {
                return false;
            }
            return true;
        }
        return $user->id === $model->id;
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('staff.create');
    }

    public function update(User $user, User $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('staff.update')) {
            if ($model->hasRole('manager')) {
                return false;
            }
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        if ($user->hasRole('manager')) {
            return $user->id !== $model->id;
        }

        if ($user->hasPermissionTo('staff.delete')) {
            if ($model->hasRole('manager')) {
                return false;
            }
            return true;
        }

        return false;
    }
}
