<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // rate limiting API: 60 request/menit per user (atau per IP jika belum login)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        /*
         * Gate (bawaan Laravel) untuk pemisahan akses tiga role:
         * - admin    : CRUD data + kelola akun
         * - pengelola: CRUD data (tidak bisa kelola akun)
         * - pengguna : hanya baca
         */
        Gate::define('manage-data', function (User $user) {
            return in_array($user->role, ['admin', 'pengelola'], true)
                ? Response::allow()
                : Response::deny('Akses ditolak. Hanya admin atau pengelola yang boleh mengubah data.');
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role === 'admin'
                ? Response::allow()
                : Response::deny('Akses ditolak. Hanya admin yang boleh mengelola akun.');
        });
    }
}
