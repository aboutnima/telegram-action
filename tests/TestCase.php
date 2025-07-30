<?php

declare(strict_types=1);

namespace Tests;

use Dotenv\Dotenv;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Aboutnima\Telegram\TelegramActionServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'TelegramAction' => \Aboutnima\Telegram\Facades\TelegramAction::class,
        ];
    }

    private function loadEnvironmentVariables(): void
    {
        // Load .env.testing from package root
        if (file_exists(dirname(__DIR__).'/.env.testing')) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__), '.env.testing');
            $dotenv->load();
        }
    }
}
