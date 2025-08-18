<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin privileges required.');
        }

        return $next($request);
    }
}