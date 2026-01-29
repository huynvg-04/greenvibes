<?php

namespace App\Policies;

use App\Http\Controllers\Admin\WarehouseController;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
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
        if ($user->hasRole('manager')) {
            return true;
        }

        return $user->hasPermissionTo('warehouse.view');
    }

    
    public function create(User $user)
    {
        if ($user->hasRole('manager')) {
            return true;
        }

        return $user->hasPermissionTo('warehouse.create');
    }
}
