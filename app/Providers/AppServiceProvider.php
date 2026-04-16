<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\AuditLog;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->configureDatabase();
        $this->registerAuthenticationEvents();
    }

    private function configureDatabase(): void
    {
        if (config('app.env') === 'production' && config('app.debug') === false) {
            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    Log::warning('Database performance', [
                        'duration_ms' => $query->time,
                    ]);
                }
            });
        }
    }

    private function registerAuthenticationEvents(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $event->user->timestamps = false;
            $event->user->last_login_at = now();
            $event->user->save();

            $this->createAuditLog('user_login', $event->user->id, $event->user->email);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                $this->createAuditLog('user_logout', $event->user->id, $event->user->email);
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            $credentials = $event->credentials ?? [];
            Log::warning('Authentication attempt failed', [
                'email' => $credentials['email'] ?? 'unknown',
                'timestamp' => now()->toIso8601String(),
            ]);
        });
    }

    private function createAuditLog(string $action, ?int $userId, string $email): void
    {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => "{$action} for user {$email}",
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent(), 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create audit log', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
