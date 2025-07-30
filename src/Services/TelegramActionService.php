<?php

declare(strict_types=1);

namespace Aboutnima\Telegram\Services;

use Illuminate\Support\Str;
use Telegram\Bot\Objects\Update;
use Aboutnima\Telegram\Contracts\TelegramActionInterface;
use ReflectionClass;

final class TelegramActionService
{
    private ?int $chatId = null;

    /** @var array<string, class-string<TelegramActionInterface>> */
    private array $actions = [];

    public function __construct()
    {
        $this->loadActions();
    }

    public function default(): self
    {
        return $this;
    }

    public function getChatId(): int
    {
        if ($this->chatId === null) {
            throw new \RuntimeException("Chat ID has not been set.");
        }

        return $this->chatId;
    }

    /**
     * Dynamically find all actions in app/Telegram and map them by their key
     */
    private function loadActions(): void
    {
        $actionPath = app_path('Telegram');
        $namespace = 'App\\Telegram';

        foreach (glob($actionPath . '/*.php') as $file) {
            $className = $namespace . '\\' . Str::replaceLast('.php', '', basename($file));

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            if (! $reflection->isInstantiable() || ! $reflection->implementsInterface(TelegramActionInterface::class)) {
                continue;
            }

            /** @var TelegramActionInterface $instance */
            $instance = app($className);
            $this->actions[$instance->key()] = $className;
        }
    }

    /**
     * Get the registered actions (mostly for debugging)
     */
    public function actions(): array
    {
        return $this->actions;
    }

    /**
     * Call an action by its key
     */
    public function callAction(string $key): void
    {
        if (!isset($this->actions[$key])) {
            throw new \InvalidArgumentException("No action found for key [$key]");
        }

        $action = app($this->actions[$key]);

        $action->handle();
    }

    public function handleRequest(Update $update): void
    {
        $message = $update->getMessage();

        $this->chatId = $message->getChat()->getId();

        $text = $message->getText();

        if ($text === '/start') {
            $this->callAction('start');
        }
    }
}
