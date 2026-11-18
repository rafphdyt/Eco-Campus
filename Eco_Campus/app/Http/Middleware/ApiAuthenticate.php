<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $plain = $request->bearerToken();

        $user = $plain
            ? User::where('api_token', hash('sha256', $plain))->first()
            : null;

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}