<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ImageUploadRateLimit
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'image-upload-' . auth()->id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            throw new ThrottleRequestsException(
                headers: ['Retry-After' => $this->limiter->availableIn($key)]
            );
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
