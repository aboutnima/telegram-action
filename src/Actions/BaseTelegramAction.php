<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\BaseTelegramActionInterface;
use Aboutnima\Telegram\Facades\TelegramAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Base class for all Telegram actions.
 * Provides default implementations for message sending settings.
 */
abstract class BaseTelegramAction implements BaseTelegramActionInterface
{
    /**
     * Action identifier
     */
    protected string $key;

    /**
     * Indicates whether the message sent by this action should be deleted
     * before the next action is executed
     */
    protected bool $deleteOnNextAction;

    /**
     * Automatically set to true when a payload is set for the next action.
     */
    private ?string $nextActionKeyNameWithPayload = null;

    /**
     * Stores the payload received from the previous action.
     */
    private array $payload = [];

    /**
     * Resolve an instance of the action via the service container.
     */
    public static function make(): self
    {
        return app(static::class);
    }

    /**
     * Get the action key.
     */
    public function getKey(): string
    {
        if (is_null($this->nextActionKeyNameWithPayload)) {
            return $this->key;
        }

        return $this->nextActionKeyNameWithPayload;
    }

    /**
     * Get the current chat ID.
     */
    public function getChatId(): int
    {
        return TelegramAction::getChatId();
    }

    /**
     * Get deleteOnNextAction value
     */
    public function getDeleteOnNextAction(): bool
    {
        return $this->deleteOnNextAction;
    }

    /**
     * Set the current payload.
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Get the current payload.
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Make action with payload
     */
    public function withPayload(array $payload): self
    {
        $newKeyName = TelegramAction::putPayloadCache($this->key, $payload);

        $this->nextActionKeyNameWithPayload = $newKeyName;

        return $this;
    }

    /**
     * Get the message text to send.
     * Override this method to customize the message.
     */
    public function message(): string
    {
        return '';
    }

    /**
     * Get the reply markup (e.g., inline keyboard).
     * Override this method to customize the markup.
     */
    public function replyMarkup(): mixed
    {
        return null;
    }

    /**
     * Handle the action: send the message and markup to Telegram.
     */
    public function handle(): mixed
    {
        $payload = [
            'chat_id' => $this->getChatId(),
            'text' => $this->message(),
        ];

        $markup = $this->replyMarkup();
        if (! blank($markup)) {
            $payload['reply_markup'] = $markup;
        }

        return Telegram::sendMessage($payload);
    }
}
