<?php

namespace Aboutnima\Telegram\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallTelegramActionCommand extends Command
{
    protected $signature = 'telegram-action:install';

    protected $description = 'Publish the Telegram config file and generate the default StartAction and UnsupportedRequestAction classes';

    public function handle(): void
    {
        $this->publishConfig();
        $startKey = $this->createStartAction();
        $unsupportedKey = $this->createUnsupportedRequestAction();

        $this->updateTelegramActionConfig($startKey, $unsupportedKey);

        $this->info('✅ Telegram Action installed successfully!');
    }

    /**
     * Publish the Telegram Action package configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('📦 Publishing telegram config...');

        $telegramConfigPath = config_path('telegram.php');
        if (! File::exists($telegramConfigPath)) {
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
    protected function createStartAction(): string
    {
        $actionPath = app_path('Telegram/StartAction.php');

        if (File::exists($actionPath)) {
            $this->warn('⚠️ StartAction already exists, skipped.');

            return config('telegram-action.start_request_key', 'start');
        }

        $this->info('🛠 Creating StartAction...');

        $randomKey = Str::random(16);

        Artisan::call('telegram-action:create-action', [
            'name' => 'StartAction',
            'key' => $randomKey,
        ]);

        $this->info('✅ StartAction created at /app/Telegram');

        return $randomKey;
    }

    /**
     * Generate the default UnsupportedRequestAction class if it doesn't already exist.
     */
    protected function createUnsupportedRequestAction(): string
    {
        $actionPath = app_path('Telegram/UnsupportedRequestAction.php');

        if (File::exists($actionPath)) {
            $this->warn('⚠️ UnsupportedRequestAction already exists, skipped.');

            return config('telegram-action.unsupported_request_key', 'unsupported');
        }

        $this->info('🛠 Creating UnsupportedRequestAction...');

        $randomKey = Str::random(16);

        Artisan::call('telegram-action:create-action', [
            'name' => 'UnsupportedRequestAction',
            'key' => $randomKey,
        ]);

        $this->info('✅ UnsupportedRequestAction created at /app/Telegram');

        return $randomKey;
    }

    /**
     * Add start/unsupported keys into telegram-action config file.
     */
    protected function updateTelegramActionConfig(string $startKey, string $unsupportedKey): void
    {
        $configPath = config_path('telegram-action.php');

        if (! File::exists($configPath)) {
            $this->error('❌ telegram-action.php config file not found.');

            return;
        }

        $config = File::get($configPath);

        $newConfig = $config;

        if (! Str::contains($config, "'start_request_key'")) {
            $newConfig = preg_replace(
                '/return\s+\[([\s\S]*?)(\];)/',
                "return [\n    'start_request_key' => '{$startKey}',\n    'unsupported_request_key' => '{$unsupportedKey}',\n$1$2",
                $config
            );
        } else {
            $newConfig = preg_replace(
                "/'start_request_key'\s*=>\s*'[^']*'/",
                "'start_request_key' => '{$startKey}'",
                $newConfig
            );

            $newConfig = preg_replace(
                "/'unsupported_request_key'\s*=>\s*'[^']*'/",
                "'unsupported_request_key' => '{$unsupportedKey}'",
                (string) $newConfig
            );
        }

        File::put($configPath, $newConfig);

        $this->info('📝 telegram-action.php updated with start and unsupported keys.');
    }
}
