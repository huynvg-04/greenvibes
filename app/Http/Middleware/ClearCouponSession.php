<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ClearCouponSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     public function handle(Request $request, Closure $next)
    {
        $checkoutRoutes = [
            'user.checkout.index',        
            'user.checkout.process',        
            'user.checkout.apply_coupon',  
            'user.checkout.remove_coupon', 
            'user.checkout.success',     
        ];

        $currentRoute = Route::currentRouteName();

        if (session()->has('coupon_code') && !in_array($currentRoute, $checkoutRoutes)) {
            session()->forget('coupon_code');
        }

        return $next($request);
    }
}


