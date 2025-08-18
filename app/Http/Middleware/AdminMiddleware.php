<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Add your admin authentication logic here
        // In production, you should implement proper authentication
        
        return $next($request);
    }
}
