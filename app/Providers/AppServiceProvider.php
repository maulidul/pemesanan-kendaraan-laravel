<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
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
        // Default string length untuk MySQL < 5.7.7 / MariaDB
        Schema::defaultStringLength(191);

        // Pakai Tailwind untuk pagination
        Paginator::useTailwind();
    }
}