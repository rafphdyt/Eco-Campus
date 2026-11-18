<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya admin yang boleh mengakses',
            ], 403);
        }

        return $next($request);
    }
}