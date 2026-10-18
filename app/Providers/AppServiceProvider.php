<?php

namespace App\Providers;

use App\Mail\Transports\BrevoTransport;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('password-reset-links', function (Request $request): Limit {
            return Limit::perMinute(5)->by($this->passwordResetRateLimitKey($request));
        });

        RateLimiter::for('password-reset-submissions', function (Request $request): Limit {
            return Limit::perMinute(5)->by($this->passwordResetRateLimitKey($request));
        });

        Mail::extend('brevo', function (array $config): BrevoTransport {
            return new BrevoTransport(
                apiKey: (string) ($config['key'] ?? ''),
                endpoint: (string) ($config['endpoint'] ?? ''),
                timeout: (int) ($config['timeout'] ?? 10),
            );
        });
    }

    private function passwordResetRateLimitKey(Request $request): string
    {
        $email = $request->input('email', '');
        $normalizedEmail = is_string($email) ? Str::lower(Str::transliterate($email)) : '';

        return 'password-reset:'.hash('sha256', $normalizedEmail).'|'.$request->ip();
    }
}
