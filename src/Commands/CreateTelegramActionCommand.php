<?php

namespace Aboutnima\Telegram\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateTelegramActionCommand extends Command
{
    protected $signature = 'telegram:create-action {name}';

    protected $description = 'Create a new Telegram action class';

    public function handle(): void
    {
        $input = trim($this->argument('name'), '/');
        $className = class_basename($input);
        $relativePath = dirname($input);

        // Define target directory and full file path
        $basePath = app_path('Telegram');
        $fullPath = $basePath . '/' . str_replace('\\', '/', $input) . '.php';

        // Build the namespace dynamically
        $namespace = 'App\\Telegram';
        if ($relativePath !== '.' && $relativePath !== '') {
            $namespace .= '\\' . str_replace(['/', '\\'], '\\', $relativePath);
        }

        if (File::exists($fullPath)) {
            $this->error("❌ {$className} already exists.");
            return;
        }

        // Ensure directory exists
        File::ensureDirectoryExists(dirname($fullPath));

        // Load stub
        $stubPath = base_path('stubs/app/Telegram/TelegramAction.stub');
        if (!File::exists($stubPath)) {
            $stubPath = __DIR__ . '/../../stubs/app/Telegram/TelegramAction.stub';
        }

        $stub = File::get($stubPath);

        // Replace placeholders
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ key }}', '{{ text }}'],
            [$namespace, $className, Str::random(), "Message text."],
            $stub
        );

        // Save file
        File::put($fullPath, $content);

        $this->info("✅ {$className} created at app/Telegram/{$input}.php");
    }
}
