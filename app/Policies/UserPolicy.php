<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['staff', 'manager']);
    }

    public function view(User $user, User $model)
    {
        return $user->hasAnyRole(['staff', 'manager']) || $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['staff', 'manager']);
    }

    public function update(User $user, User $model)
    {
        return $user->hasAnyRole(['staff', 'manager']) || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        return $user->hasRole('manager');
    }
}
