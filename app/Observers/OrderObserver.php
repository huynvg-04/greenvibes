<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\MembershipTier;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        if ($order->status == 'completed' && $order->getOriginal('status') != 'completed') {
            $this->updateUserRank($order->user);
        }

        if ($order->status == 'cancelled' && $order->getOriginal('status') != 'cancelled') {
     
            \App\Models\TierUsageLog::where('order_id', $order->id)->delete();

            if ($order->getOriginal('status') == 'completed') {
                $this->updateUserRank($order->user);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }

    public static function updateUserRank($user)
    {
        if (!$user || !$user->customerProfile) return;

        $profile = $user->customerProfile;

        $query = $user->orders()->where('status', 'completed');
        $query->whereDoesntHave('returns', function ($q) {
            $q->where('status', 'approved');
        });

        $totalSpent = $query->sum('total_amount');
        $totalOrders = $query->count();

        $profile->total_spent_lifetime = $totalSpent;
        $profile->total_orders_lifetime = $totalOrders;

        $newTier = MembershipTier::where(function ($q) use ($totalSpent, $totalOrders) {
            $q->where('min_spent', '<=', $totalSpent)
                ->orWhere('min_orders', '<=', $totalOrders);
        })
            ->orderBy('rank_priority', 'desc')
            ->first();

        if ($newTier && $profile->membership_tier_id !== $newTier->id) {

            $oldTierId = $profile->membership_tier_id;
            $oldTierPriority = 0;

            if ($oldTierId) {
                $oldTier = MembershipTier::find($oldTierId);
                $oldTierPriority = $oldTier ? $oldTier->rank_priority : 0;
            }

            $profile->membership_tier_id = $newTier->id;
            $profile->level_updated_at = now();

            if ($newTier->validity_days) {
                $profile->level_expires_at = now()->addDays($newTier->validity_days);
            } else {
                $profile->level_expires_at = null;
            }

            if ($newTier->rank_priority > $oldTierPriority) {
                try {
                    $user->notify(new \App\Notifications\LevelUpNotification($newTier));
                } catch (\Exception $e) {
                }
            }
        }

        $profile->save();
    }
}
