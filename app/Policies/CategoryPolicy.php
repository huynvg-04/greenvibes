<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

   public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('category.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('category.view');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('category.create');
    }

    public function update(User $user, Category $category)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('category.update');
    }

    public function delete(User $user, Category $category)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('category.delete');
    }
}
