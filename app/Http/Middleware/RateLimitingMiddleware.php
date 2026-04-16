<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitingMiddleware
{
    public function __construct(protected RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->getKey($request);
        $maxAttempts = 60;
        $decayMinutes = 1;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => $this->limiter->availableIn($key),
            ], 429)->header('Retry-After', $this->limiter->availableIn($key));
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);
        
        return $response
            ->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', max(0, $maxAttempts - $this->limiter->attempts($key)))
            ->header('X-RateLimit-Reset', $this->limiter->resetAfter($key));
    }

    private function getKey(Request $request): string
    {
        $identifier = $request->user()?->id ?? hash('sha256', $request->ip());
        return 'rate_limit:' . $identifier . ':' . $request->path();
    }
}
