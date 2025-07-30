<?php

namespace Aboutnima\LaravelZoom;

use Aboutnima\LaravelZoom\Auth\ZoomTokenManager;
use Aboutnima\LaravelZoom\Services\TelegramActionService;
use Illuminate\Support\ServiceProvider;

class TelegramActionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge the configuration file
        $this->mergeConfigFrom(__DIR__.'/../config/telegram-action.php', 'telegram-action');

        // Register the ZoomService singleton and define TokenManager class with data
        $this->app->singleton('telegram-action', fn (): TelegramActionService => new TelegramActionService());
    }

    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/telegram-action.php' => config_path('telegram-action.php'),
        ], 'telegram-action-config');
    }
}
