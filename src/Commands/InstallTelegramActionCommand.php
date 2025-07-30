<?php

namespace Aboutnima\Telegram\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallTelegramActionCommand extends Command
{
    protected $signature = 'telegram-action:install';

    protected $description = 'Publish the Telegram config file and generate the default StartAction class';

    public function handle(): void
    {
        $this->publishConfig();

        $this->createStartAction();

        $this->info('✅ Telegram Action installed successfully!');
    }

    /**
     * Publish the Telegram Action package configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('📦 Publishing config...');

        Artisan::call('vendor:publish', [
            '--tag' => 'telegram-action-config',
            '--force' => true,
        ]);

        $this->info('✅ Config published to config/telegram-action.php');
    }

    /**
     * Generate the default StartAction class if it doesn't already exist.
     */
    protected function createStartAction(): void
    {
        $actionPath = app_path('Telegram/StartAction.php');

        if (File::exists($actionPath)) {
            $this->warn('⚠️ StartAction already exists, skipped.');
            return;
        }

        $this->info('🛠 Creating StartAction...');

        Artisan::call('telegram:create-action', [
            'name' => 'StartAction',
        ]);

        $this->info('✅ StartAction created at /app/Telegram');
    }
}
