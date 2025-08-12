<?php

namespace App\Providers;

use App\Services\WhatsappService;
use Illuminate\Support\ServiceProvider;

class WhatsappServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsappService::class, function ($app) {
            return new WhatsappService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // You can add any boot logic here
    }
}
