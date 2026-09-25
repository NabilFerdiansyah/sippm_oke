<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Halaman utama default (fallback), tidak dipakai langsung karena
     * redirect setelah login selalu disesuaikan dengan peran (role) akun.
     */
    public const HOME = '/dashboard';

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->input('username').'|'.$request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Tentukan halaman dashboard pertama sesuai peran akun yang login.
     */
    public static function homeForRole(string $role): string
    {
        return match ($role) {
            'manager' => route('manager.dashboard'),
            'teknisi' => route('teknisi.dashboard'),
            default => route('operator.dashboard'),
        };
    }
}
