<?php

namespace Aboutnima\Telegram\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateTelegramActionCommand extends Command
{
    protected $signature = 'telegram-action:create-action {name} {key?}';

    protected $description = 'Create a new Telegram action class';

    public function handle(): void
    {
        $input = trim($this->argument('name'), '/');
        $key = $this->argument('key') ?? Str::random(24);
        $className = class_basename($input);
        $relativePath = dirname($input);

        $namespace = $this->buildNamespace($relativePath);
        $fullPath = $this->buildTargetPath($input);

        // Prevent overwriting existing classes
        if (File::exists($fullPath)) {
            $this->error("❌ {$className} already exists at: {$fullPath}");

            return;
        }

        // Ensure the target directory exists
        File::ensureDirectoryExists(dirname($fullPath));

        // Load stub content
        $stub = $this->loadStub();

        // Replace placeholders in the stub
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ key }}', '{{ message }}'],
            [$namespace, $className, $key ?? Str::random(), "Message from `{$className}` action"],
            $stub
        );

        // Write the generated class to the filesystem
        File::put($fullPath, $content);

        // Show success message
        $this->info("✅ {$className} created at: ".Str::of($fullPath)->after(base_path().'/'));
    }

    /**
     * Build the PSR-4 namespace for the given path.
     */
    protected function buildNamespace(string $relativePath): string
    {
        $namespace = 'App\\Telegram';

        if ($relativePath !== '.' && $relativePath !== '') {
            $namespace .= '\\'.str_replace(['/', '\\'], '\\', $relativePath);
        }

        return $namespace;
    }

    /**
     * Build the full file path where the class will be generated.
     */
    protected function buildTargetPath(string $input): string
    {
        return app_path('Telegram/'.str_replace('\\', '/', $input).'.php');
    }

    /**
     * Load the stub file for generating the action class.
     * Falls back to package stub if no published stub is found.
     *
     * @throws FileNotFoundException
     */
    protected function loadStub(): string
    {
        // Path to published stub (if user has customized it)
        $published = base_path('stubs/app/Telegram/TelegramAction.stub');

        // Path to package's internal default stub
        $package = __DIR__.'/../../stubs/app/Telegram/TelegramAction.stub';

        if (File::exists($published)) {
            return File::get($published);
        }

        if (File::exists($package)) {
            return File::get($package);
        }

        throw new FileNotFoundException('TelegramAction.stub not found in published or package path.');
    }
}
