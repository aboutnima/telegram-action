<?php

namespace Aboutnima\Telegram\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallTelegramActionCommand extends Command
{
    protected $signature = 'telegram-action:install';

    protected $description = 'Publish telegram config and create StartTelegramAction';

    public function handle(): void
    {
        // Publish config file
        $this->info('Publishing config...');

        Artisan::call('vendor:publish', [
            '--tag' => 'telegram-action-config',
            '--force' => true
        ]);

        $this->info('✅ Config published to config/telegram-action.php');

        // Create initial StartTelegramAction class
        $actionPath = app_path('Telegram/StartTelegramAction.php');
        if (!File::exists($actionPath)) {
            $this->info('Creating StartTelegramAction...');

            Artisan::call('telegram:create-action', [
                'name' => 'StartTelegramAction'
            ]);

            $this->info('✅ StartTelegramAction created at /app/Telegram');
        } else {
            $this->warn('⚠️ StartTelegramAction already exists, skipped.');
        }

        $this->info('✅Telegram Action installed successfully!');
    }
}
