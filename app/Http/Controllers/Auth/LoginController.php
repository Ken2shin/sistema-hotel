<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const ATTEMPT_DECAY_MINUTES = 60;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'lowercase'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'El correo es requerido.',
            'email.email' => 'Correo inválido.',
            'email.lowercase' => 'Correo debe estar en minúsculas.',
            'password.required' => 'Contraseña es requerida.',
            'password.min' => 'Contraseña debe tener mínimo 8 caracteres.',
        ]);

        $email = $validated['email'];
        
        if ($this->isAccountLocked($email)) {
            $this->logSecurityEvent('login_attempt_locked_account', $request, ['email' => $email]);
            return back()->withErrors([
                'email' => 'Cuenta bloqueada. Intenta más tarde.'
            ])->withInput($request->only('email'));
        }

        if (Auth::attempt(['email' => $email, 'password' => $validated['password']], $request->boolean('remember'))) {
            $this->clearFailedAttempts($email);
            $this->logSecurityEvent('login_success', $request, ['email' => $email]);
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'));
        }

        $this->recordFailedAttempt($email, $request);
        $this->logSecurityEvent('login_failed', $request, ['email' => $email]);

        return back()->withErrors([
            'email' => 'Credenciales inválidas.'
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $this->logSecurityEvent('logout', $request, ['user_id' => auth()->id()]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }

    private function recordFailedAttempt(string $email, Request $request): void
    {
        $key = $this->getAttemptKey($email);
        $attempts = RateLimiter::increment($key, self::ATTEMPT_DECAY_MINUTES);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $lockKey = $this->getLockKey($email);
            RateLimiter::hit($lockKey, self::LOCKOUT_MINUTES);
            
            Log::critical('Account lockout triggered', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
                'attempts' => $attempts,
                'locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
            ]);
        }
    }

    private function isAccountLocked(string $email): bool
    {
        $key = $this->getLockKey($email);
        return RateLimiter::tooManyAttempts($key, 1, self::LOCKOUT_MINUTES);
    }

    private function clearFailedAttempts(string $email): void
    {
        $attemptKey = $this->getAttemptKey($email);
        $lockKey = $this->getLockKey($email);
        
        RateLimiter::clear($attemptKey);
        RateLimiter::clear($lockKey);
    }

    private function getAttemptKey(string $email): string
    {
        return 'login_attempts:' . hash('sha256', strtolower($email));
    }

    private function getLockKey(string $email): string
    {
        return 'login_lock:' . hash('sha256', strtolower($email));
    }

    private function logSecurityEvent(string $event, Request $request, array $context = []): void
    {
        $data = array_merge([
            'event' => $event,
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 255),
            'timestamp' => now()->toIso8601String(),
        ], $context);

        Log::channel('security')->notice("Security: {$event}", $data);
    }
}
