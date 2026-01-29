<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->hasRole('manager')) return true;

        return $user->hasPermissionTo('order.view');
    }

    public function viewCustomer(User $user)
    {
        if ($user->hasRole('customer')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Order $order)
    {
        if ($user->hasAnyRole(['manager', 'staff'])) {
            return $user->hasPermissionTo('order.show');
        }

        return $user->id === $order->user_id;
    }


    public function create(User $user)
    {
        return true;
    }


    public function update(User $user, Order $order)
    {
        if ($user->hasAnyRole(['manager', 'staff'])) {
            return $user->hasPermissionTo('order.update');
        }

        if ($user->id === $order->user_id && $order->status === 'pending') {
            return true;
        }

        return false;
    }

    public function cancel(User $user, Order $order)
    {
        if ($user->hasAnyRole(['manager', 'staff'])) {
            return $user->hasPermissionTo('order.update');
        }

        return $user->id === $order->user_id && $order->status === 'pending';
    }

    public function delete(User $user, Order $order)
    {
        if ($user->hasRole('manager')) {
            return true;
        }
        return false;
    }
}
