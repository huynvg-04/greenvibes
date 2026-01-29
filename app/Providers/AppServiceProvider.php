<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderReturnObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);

        OrderReturn::observe(OrderReturnObserver::class);

        Product::observe(ProductObserver::class);
    }
}
