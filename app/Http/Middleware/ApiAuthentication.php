<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginSession;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return $this->unauthorizedResponse('Token not provided');
        }

        // Try to authenticate using the token as session_id
        $session = LoginSession::where('session_id', $token)
            ->where('expires_at', '>', now())
            ->first();

        if ($session) {
            $user = User::find($session->user_id);
            
            if ($user) {
                Auth::setUser($user);
                $request->merge([
                    'wallet_id' => $session->wallet_id,
                    'session_id' => $session->session_id
                ]);
                
                return $next($request);
            }
        }

        // Try Laravel Sanctum or Passport if configured
        if (Auth::guard('sanctum')->check()) {
            return $next($request);
        }

        return $this->unauthorizedResponse('Invalid or expired token');
    }

    /**
     * Get the token from the request
     */
    private function getTokenFromRequest(Request $request): ?string
    {
        // Check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check X-API-Token header
        $apiToken = $request->header('X-API-Token');
        if ($apiToken) {
            return $apiToken;
        }

        // Check X-Session-ID header
        $sessionId = $request->header('X-Session-ID');
        if ($sessionId) {
            return $sessionId;
        }

        // Check query parameter
        return $request->input('token') ?? $request->input('session_id');
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => 401
        ], 401);
    }
}