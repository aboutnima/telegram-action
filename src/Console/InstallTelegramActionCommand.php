<?php

namespace Aboutnima\Telegram\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallTelegramActionCommand extends Command
{
    protected $signature = 'telegram-action:install';

    protected $description = 'Publish the Telegram config file and generate the default StartAction and UnsupportedRequestAction classes';

    public function handle(): void
    {
        $this->publishConfig();
        $this->createStartAction();
        $this->createUnsupportedRequestAction();

        $this->info('✅ Telegram Action installed successfully!');
    }

    /**
     * Publish the Telegram Action package configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('📦 Publishing telegram config...');

        // Only publish telegram-config if it doesn't exist
        $telegramConfigPath = config_path('telegram.php');
        if (!File::exists($telegramConfigPath)) {
            Artisan::call('vendor:publish', [
                '--tag' => 'telegram-config',
            ]);
            $this->info('✅ telegram.php published to config/');
        } else {
            $this->warn('⚠️ telegram.php already exists, skipping publish.');
        }

        $this->info('📦 Publishing telegram action config...');

        Artisan::call('vendor:publish', [
            '--tag' => 'telegram-action-config',
            '--force' => true,
        ]);

        $this->info('✅ telegram-action.php published to config/');
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

        Artisan::call('telegram-action:create-action', [
            'name' => 'StartAction',
            'key' => 'start',
        ]);

        $this->info('✅ StartAction created at /app/Telegram');
    }

    /**
     * Generate the default UnsupportedRequestAction class if it doesn't already exist.
     */
    protected function createUnsupportedRequestAction(): void
    {
        $actionPath = app_path('Telegram/UnsupportedRequestAction.php');

        if (File::exists($actionPath)) {
            $this->warn('⚠️ UnsupportedRequestAction already exists, skipped.');
            return;
        }

        $this->info('🛠 Creating UnsupportedRequestAction...');

        Artisan::call('telegram-action:create-action', [
            'name' => 'UnsupportedRequestAction',
            'key' => 'unsupported',
        ]);

        $this->info('✅ UnsupportedRequestAction created at /app/Telegram');
    }
}
