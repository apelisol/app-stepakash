<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If the user is authenticated via API, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are already authenticated.'
                    ], 403);
                }
                
                // For web requests, redirect to the dashboard
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}