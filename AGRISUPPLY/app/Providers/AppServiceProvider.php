<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Laravel UI Service Provider if not auto-discovered
        if (class_exists(\Laravel\Ui\UiServiceProvider::class)) {
            $this->app->register(\Laravel\Ui\UiServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}