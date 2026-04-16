<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (auth()->check() && auth()->user()->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'No tiene los permisos necesarios'
        ], 403);
    }
}
