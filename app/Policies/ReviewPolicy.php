<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
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
        return $user->hasPermissionTo('review.view');
    }

    public function view(User $user, Review $review)
    {
        if ($user->hasAnyRole(['manager', 'staff'])) {
            return $user->hasPermissionTo('review.view');
        }
        return true;
    }


    public function create(User $user)
    {
        return $user->hasRole('customer');
    }


    public function delete(User $user, Review $review)
    {
        if ($user->hasAnyRole(['manager', 'staff'])) {
            return $user->hasPermissionTo('review.delete');
        }

        return false;
    }
}
