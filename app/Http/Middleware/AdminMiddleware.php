<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('Administrador')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'No tiene permisos para acceder a este recurso'
        ], 403);
    }
}
