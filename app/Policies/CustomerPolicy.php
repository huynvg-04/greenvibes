<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('customer.view');
    }

    public function view(User $user, User $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('customer.view')) {
            if ($model->hasAnyRole(['manager', 'staff']) && $user->id !== $model->id) {
                return false;
            }
            return true;
        }
        return $user->id === $model->id;
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('customer.create');
    }

    public function update(User $user, User $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('customer.update')) {
            if ($model->hasAnyRole(['manager', 'staff'])) {
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

        if ($user->hasPermissionTo('customer.delete')) {
            if ($model->hasAnyRole(['manager', 'staff'])) {
                return false;
            }
            return true;
        }

        return false;
    }
}
