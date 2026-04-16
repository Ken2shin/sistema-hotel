<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputValidationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldValidateInput($request)) {
            $this->validateInputSafety($request);
        }

        return $next($request);
    }

    private function shouldValidateInput(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    private function validateInputSafety(Request $request): void
    {
        $maxSize = 10485760;
        
        if ($request->getContentLength() > $maxSize) {
            abort(413, 'Request payload too large');
        }

        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                if (strlen($value) > 5000) {
                    abort(400, 'Input field exceeds maximum length');
                }

                if ($this->containsDangerousPatterns($value)) {
                    abort(422, 'Invalid input detected');
                }
            }
        }
    }

    private function containsDangerousPatterns(string $input): bool
    {
        $patterns = [
            '/<script[^>]*>/i',
            '/on\w+\s*=/i',
            '/javascript:/i',
            '/data:text\/html/i',
            '/eval\(/i',
            '/base64_decode/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
