<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->bind(\App\Services\AuthService::class);
        $this->app->bind(\App\Services\UserService::class);
        $this->app->bind(\App\Services\LoanService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);
        
        // Set timezone
        date_default_timezone_set(config('app.timezone', 'Asia/Ho_Chi_Minh'));
    }
}
