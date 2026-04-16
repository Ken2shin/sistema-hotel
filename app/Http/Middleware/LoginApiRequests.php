<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        Log::channel('api')->info('API Request', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'status' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'duration_ms' => round($duration * 1000, 2),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $response;
    }
}
