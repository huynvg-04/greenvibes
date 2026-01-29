<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlogPolicy
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
        return $user->hasPermissionTo('blog.view');
    }

    public function view(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('blog.show');
    }

    public function create(User $user)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('blog.create');
    }

    public function update(User $user, Blog $blog)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('blog.update');
    }

    public function delete(User $user, Blog $blog)
    {
        if ($user->hasRole('manager')) return true;
        return $user->hasPermissionTo('blog.delete');
    }
}
