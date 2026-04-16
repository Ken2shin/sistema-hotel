<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ThrottleLogins
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->method() !== 'POST') {
            return $next($request);
        }

        $email = (string) $request->input('email');
        $key = 'login-attempts:' . hash('sha256', $email);
        $maxAttempts = 3;
        $decayMinutes = 1;

        // Verificar si la IP está bloqueada
        $ipBlockKey = 'login-block:' . $this->getClientIp($request);
        if ($this->limiter->tooManyAttempts($ipBlockKey, 1, $decayMinutes)) {
            Log::warning('Login bloqueado por múltiples intentos fallidos', [
                'email' => $email,
                'ip' => $this->getClientIp($request),
                'timestamp' => now(),
            ]);

            return response()
                ->view('auth.blocked', [], 429)
                ->header('Retry-After', $this->limiter->availableIn($ipBlockKey));
        }

        // Permitir el intento y continuar
        $response = $next($request);

        // Si la autenticación falló, incrementar contador
        if ($response->status() === 401 || $response->getStatusCode() === 401) {
            $this->incrementAttempts($key, $email, $request);
        } else {
            // Si fue exitoso, limpiar intentos
            $this->limiter->clear($key);
            $this->limiter->clear($ipBlockKey);
        }

        return $response;
    }

    protected function incrementAttempts($key, $email, Request $request)
    {
        $attempts = $this->limiter->attempts($key);
        $this->limiter->hit($key, 60); // 60 segundos = 1 minuto

        Log::warning('Intento de login fallido', [
            'email' => $email,
            'ip' => $this->getClientIp($request),
            'attempts' => $attempts + 1,
            'timestamp' => now(),
        ]);

        if ($attempts + 1 >= 3) {
            // Bloquear la IP después del tercer intento fallido
            $ipBlockKey = 'login-block:' . $this->getClientIp($request);
            $this->limiter->hit($ipBlockKey, 60);

            // Enviar notificación al administrador
            $this->notifyAdmins([
                'email' => $email,
                'ip' => $this->getClientIp($request),
                'user_agent' => $request->userAgent(),
                'attempts' => $attempts + 1,
                'timestamp' => now(),
            ]);
        }
    }

    protected function notifyAdmins($data)
    {
        try {
            Log::critical('Alerta de Seguridad: Múltiples intentos fallidos de login', $data);
            
            // Aquí puedes agregar envío de email
            // Mail::to('admin@hotel.com')->send(new LoginAttemptNotification($data));
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de login fallido', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function getClientIp(Request $request): string
    {
        return $request->ip() ?? '0.0.0.0';
    }
}
