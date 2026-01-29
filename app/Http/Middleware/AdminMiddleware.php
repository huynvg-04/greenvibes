<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['manager', 'staff'])) {
            return $next($request); 
        }


        return redirect('/home')->with('error', 'Bạn không có quyền truy cập');
    }
}