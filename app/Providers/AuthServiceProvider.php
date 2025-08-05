<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Pastikan ini di-import

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Kode ini akan selalu memberikan akses penuh kepada user dengan role 'Super Admin'
        // tanpa perlu memeriksa hak akses satu per satu.
        Gate::before(function ($user, $ability) {
            // Pastikan model User Anda memiliki relasi atau metode hasRole
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}