<?php

namespace Aboutnima\Telegram;

use Illuminate\Support\ServiceProvider;
use Aboutnima\Telegram\Services\TelegramActionService;

class TelegramActionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge the configuration file
        $this->mergeConfigFrom(__DIR__.'/../config/telegram-action.php', 'telegram-action');

        // Register the TelegramAction singleton and define TokenManager class with data
        $this->app->singleton('telegram-action', fn (): TelegramActionService => new TelegramActionService());
    }

    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/telegram-action.php' => config_path('telegram-action.php'),
        ], 'telegram-action-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\InstallTelegramActionCommand::class,
                Commands\CreateTelegramActionCommand::class,
            ]);
        }
    }
}
