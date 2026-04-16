<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->header('Content-Security-Policy', 
            "default-src 'self'; script-src 'self'; style-src 'self' 'nonce-" . $this->getNonce() . "'; img-src 'self' data: https:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';"
        );
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');
        $response->header('X-Permitted-Cross-Domain-Policies', 'none');
        $response->header('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->header('Cross-Origin-Opener-Policy', 'same-origin');
        $response->header('Cross-Origin-Resource-Policy', 'same-origin');

        if (!$response->headers->has('Cache-Control')) {
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        }

        return $response;
    }

    private function getNonce(): string
    {
        return base64_encode(bin2hex(random_bytes(16)));
    }
}
