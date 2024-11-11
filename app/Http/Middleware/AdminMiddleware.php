<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has the 'admin' role
        $user = $request->user();

        // Log the user role for debugging purposes
        Log::info('Checking user role for admin access', ['user_id' => $user ? $user->id : null, 'role' => $user ? $user->role : 'user']);

        if ($user && $user->role === 'admin') {
            // Proceed to the next middleware if the user is an admin
            return $next($request);
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt by non-admin user', ['user_id' => $user ? $user->id : null]);

        // Return an Unauthorized response
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
