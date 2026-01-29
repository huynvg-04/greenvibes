<?php

namespace App\Observers;

use App\Models\OrderReturn;
use App\Observers\OrderObserver;

class OrderReturnObserver
{
    /**
     * Handle the OrderReturn "created" event.
     *
     * @param  \App\Models\OrderReturn  $orderReturn
     * @return void
     */
    public function created(OrderReturn $orderReturn)
    {
        //
    }

    /**
     * Handle the OrderReturn "updated" event.
     *
     * @param  \App\Models\OrderReturn  $orderReturn
     * @return void
     */
    public function updated(OrderReturn $orderReturn)
    {
        if ($orderReturn->status == 'approved' && $orderReturn->getOriginal('status') != 'approved') {
            
            $order = $orderReturn->order; 
            
            if ($order && $order->user) {
                OrderObserver::updateUserRank($order->user);
            }
        }
    }

    /**
     * Handle the OrderReturn "deleted" event.
     *
     * @param  \App\Models\OrderReturn  $orderReturn
     * @return void
     */
    public function deleted(OrderReturn $orderReturn)
    {
        //
    }

    /**
     * Handle the OrderReturn "restored" event.
     *
     * @param  \App\Models\OrderReturn  $orderReturn
     * @return void
     */
    public function restored(OrderReturn $orderReturn)
    {
        //
    }

    /**
     * Handle the OrderReturn "force deleted" event.
     *
     * @param  \App\Models\OrderReturn  $orderReturn
     * @return void
     */
    public function forceDeleted(OrderReturn $orderReturn)
    {
        //
    }
}
