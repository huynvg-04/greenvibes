<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $user = Auth::user();

            $isUserBlocked = ($user->status === 'blocked');

            $isProfileBlocked = false;
            if ($user->customerProfile) {
                if ($user->customerProfile->status === 'blocked') {
                    $isProfileBlocked = true;
                }
            }

            if ($isUserBlocked || $isProfileBlocked) {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->is('admin/*')) {
                    return redirect()->route('auth.admin-login')->with('error', 'Tài khoản của bạn đã bị khóa quyền truy cập.');
                }

                return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ CSKH.');
            }
        }

        return $next($request);
    }
}
