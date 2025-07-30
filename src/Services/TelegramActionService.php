<?php

declare(strict_types=1);

namespace Aboutnima\Telegram\Services;

use Aboutnima\Telegram\Contracts\BaseTelegramActionInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

/**
 * Service to manage and dispatch Telegram bot actions.
 */
final class TelegramActionService
{
    /**
     * Current chat ID extracted from the incoming update.
     */
    private ?int $chatId = null;

    /**
     * Registered Telegram action classes indexed by their unique keys.
     *
     * @var array<string, class-string<BaseTelegramActionInterface>>
     */
    private array $actions = [];

    public function __construct()
    {
        $this->loadActions();
    }

    /**
     * Return the current instance — useful as a default or fluent accessor.
     */
    public function default(): self
    {
        return $this;
    }

    /**
     * Load and register all Telegram actions from the app/Telegram directory.
     * Actions must implement the TelegramActionInterface and be instantiable.
     */
    private function loadActions(): void
    {
        $actionPath = app_path('Telegram');
        $namespace = 'App\\Telegram';

        foreach (glob($actionPath.'/*.php') as $file) {
            $className = $namespace.'\\'.Str::replaceLast('.php', '', basename($file));

            if (! class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            if (! $reflection->isInstantiable()) {
                continue;
            }
            if (! $reflection->implementsInterface(BaseTelegramActionInterface::class)) {
                continue;
            }

            /** @var BaseTelegramActionInterface $instance */
            $instance = app($className);

            $this->actions[$instance->getKey()] = $className;
        }
    }

    /**
     * Get the current chat ID.
     *
     * @throws \RuntimeException if chat ID is not set.
     */
    public function getChatId(): int
    {
        if ($this->chatId === null) {
            throw new \RuntimeException('Chat ID has not been set before calling getChatId().');
        }

        return $this->chatId;
    }

    /**
     * Return all registered actions (mostly useful for debugging or introspection).
     *
     * @return array<string, class-string<BaseTelegramActionInterface>>
     */
    public function actions(): array
    {
        return $this->actions;
    }

    /**
     * Get the registered action key for the given action class name.
     *
     * @param  class-string<BaseTelegramActionInterface>  $class
     * @return string
     *
     * @throws \InvalidArgumentException if the class is not registered.
     */
    public function getActionKey(string $class): string
    {
        $key = array_search($class, $this->actions, true);

        if ($key === false) {
            throw new \InvalidArgumentException("Action class '{$class}' is not registered.");
        }

        return $key;
    }

    /**
     * Call a registered action by its key.
     *
     * @param  string  $key  The unique key of the action.
     *
     * @throws \InvalidArgumentException if no action is found for the given key.
     */
    public function callAction(string $key): mixed
    {
        if (! isset($this->actions[$key])) {
            throw new \InvalidArgumentException("No registered Telegram action for key: '{$key}'.");
        }

        $previousState = Cache::get($this->getChatId());

        if ($previousState) {
            $previousAction = app($this->actions[$previousState['action_key']]);
            if ($previousAction->getDeleteOnNextAction()) {
                Telegram::deleteMessage([
                    'chat_id' => $this->getChatId(),
                    'message_id' => $previousState['message_id'],
                ]);
            }
        }

        /** @var BaseTelegramActionInterface $action */
        $action = app($this->actions[$key]);
        $action->setChatId($this->getChatId());

        return $action->handle();
    }

    /**
     * Handle an incoming Telegram update.
     *
     * @param  Update  $update  The update object from Telegram webhook.
     */
    public function handleRequest(Update $update): void
    {
        $actionKey = '';

        $message = $update->getMessage();
        $callback = $update->getCallbackQuery();
        $this->chatId = $message->getChat()->getId();
        $text = $message->getText();

        if ($text === '/start') {
            $actionKey = 'start';
        } else if ($callback) {
            $actionKey = $callback->getData();
        }

        $response = $this->callAction($actionKey);

        Cache::put($this->getChatId(), [
            'message_id' => $response->getMessageId(),
            'action_key' => $actionKey
        ], now()->addMinutes(5));
    }
}
