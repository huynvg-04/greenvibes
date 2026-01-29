<?php

namespace App\Policies;

use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderReturnPolicy
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
        
        return $user->hasPermissionTo('return.view');
    }

    public function view(User $user, OrderReturn $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('return.show')) {
            return true;
        }

        return $user->id === $model->user_id;
    }


    public function update(User $user, OrderReturn $model)
    {
        if ($user->hasRole('manager')) return true;

        if ($user->hasPermissionTo('return.update')) {
            return true;
        }

        return $user->id === $model->user_id && $model->status === 'pending';
    }

}