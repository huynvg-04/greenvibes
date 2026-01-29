<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\RevenuePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\Coupon::class => \App\Policies\CouponPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Banner::class => \App\Policies\BannerPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Blog::class => \App\Policies\BlogPolicy::class,
        \App\Models\ProductVariant::class => \App\Policies\ProductVariantPolicy::class,
        \App\Models\ProductImage::class => \App\Policies\ProductImagePolicy::class,
        \App\Models\User::class => \App\Policies\CustomerPolicy::class,
        \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
        \App\Models\Attribute::class => \App\Policies\AttributePolicy::class,
        \App\Models\WarehouseTransaction::class => \App\Policies\WarehousePolicy::class,
        \App\Models\PaymentMethod::class => \App\Policies\PaymentMethodPolicy::class,
        \App\Models\ShippingRate::class => \App\Policies\ShippingRatePolicy::class,
        \App\Models\MembershipTier::class => \App\Policies\MembershipTierPolicy::class,
        \App\Models\StaffProfile::class => \App\Policies\StaffPolicy::class,
        \App\Models\CustomerProfile::class => \App\Policies\CustomerPolicy::class,
        \App\Models\MembershipTier::class => \App\Policies\MembershipTierPolicy::class,
        \App\Models\OrderReturn::class => \App\Policies\OrderReturnPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('manager')) {
                return true;
            }
        });
        Gate::policy('revenue', RevenuePolicy::class);
    }
}
