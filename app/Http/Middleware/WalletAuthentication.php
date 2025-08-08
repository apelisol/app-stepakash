<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginSession;
use Symfony\Component\HttpFoundation\Response;

class WalletAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via standard Laravel auth
        if (Auth::check()) {
            return $next($request);
        }

        // Check for wallet session authentication
        $sessionId = $request->header('X-Session-ID') ?? $request->input('session_id');
        
        if ($sessionId) {
            $session = LoginSession::where('session_id', $sessionId)
                ->where('expires_at', '>', now())
                ->first();

            if ($session) {
                // Set the authenticated user for this request
                Auth::loginUsingId($session->user_id);
                
                // Add wallet info to request
                $request->merge(['wallet_id' => $session->wallet_id]);
                
                return $next($request);
            }
        }

        // Return unauthorized response for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please login to continue.'
            ], 401);
        }

        // Redirect to login for web requests
        return redirect()->route('login');
    }
}