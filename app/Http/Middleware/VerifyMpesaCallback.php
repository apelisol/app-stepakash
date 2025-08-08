<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyMpesaCallback
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log all incoming M-Pesa callbacks for debugging
        Log::info('M-Pesa Callback Received', [
            'url' => $request->url(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_body' => $request->getContent(),
            'ip' => $request->ip(),
        ]);

        // Verify the request is coming from M-Pesa
        if (!$this->isValidMpesaRequest($request)) {
            Log::warning('Invalid M-Pesa callback request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url()
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Invalid request'
            ], 400);
        }

        // Verify request signature if you have one
        if (!$this->verifySignature($request)) {
            Log::warning('M-Pesa callback signature verification failed', [
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Signature verification failed'
            ], 400);
        }

        return $next($request);
    }

    /**
     * Verify if the request is from M-Pesa
     */
    private function isValidMpesaRequest(Request $request): bool
    {
        // Get allowed M-Pesa IPs from config
        $allowedIps = config('mpesa.callback_ips', [
            '196.201.214.200',
            '196.201.214.206',
            '196.201.213.114',
            '196.201.214.207',
            '196.201.214.208',
            '196.201.213.44',
            '196.201.212.127',
            '196.201.212.138',
            '196.201.212.129',
            '196.201.212.136',
            '196.201.212.74'
        ]);

        $requestIp = $request->ip();

        // Allow localhost for testing
        if (app()->environment(['local', 'testing'])) {
            $allowedIps[] = '127.0.0.1';
            $allowedIps[] = '::1';
        }

        return in_array($requestIp, $allowedIps);
    }

    /**
     * Verify request signature (implement if M-Pesa provides signatures)
     */
    private function verifySignature(Request $request): bool
    {
        // For now, return true. Implement signature verification if M-Pesa provides it
        return true;

        /*
        // Example implementation if signatures are provided:
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();
        $secret = config('mpesa.callback_secret');

        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
        */
    }
}