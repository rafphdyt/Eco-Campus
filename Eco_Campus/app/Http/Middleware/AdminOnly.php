<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh masuk.');
        }
        return $next($request);
    }
}