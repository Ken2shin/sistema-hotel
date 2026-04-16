<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureCorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = $this->getAllowedOrigins();
        $origin = $request->headers->get('Origin');

        if ($origin && in_array($origin, $allowedOrigins)) {
            $response = $next($request);
            
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '3600');
            $response->header('Access-Control-Expose-Headers', 'Content-Length, X-JSON-Response');

            return $response;
        }

        return $next($request);
    }

    private function getAllowedOrigins(): array
    {
        $origins = [
            config('app.url'),
        ];

        if (config('app.env') === 'local') {
            $origins[] = 'http://localhost:3000';
            $origins[] = 'http://localhost:8000';
            $origins[] = 'http://127.0.0.1:3000';
            $origins[] = 'http://127.0.0.1:8000';
        }

        return $origins;
    }
}
