<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\BaseTelegramActionInterface;
use Aboutnima\Telegram\Facades\TelegramAction;
use Telegram\Bot\Keyboard\Keyboard;
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
     * Get the inline keyboard markup
     * Override this method to customize the inline keyboard markup.
     */
    public function inlineKeyboardMarkup(): array
    {
        return [];
    }

    /**
     * Get the reply keyboard markup
     * Override this method to customize the reply keyboard markup.
     */
    public function replyKeyboardMarkup(): array
    {
        return [];
    }

    /**
     * Generate the final keyboard markup based on reply and inline keyboards.
     * Prefers reply markup if available; falls back to inline markup otherwise.
     */
    public function generateReplyMarkup(): mixed
    {
        $reply = $this->replyKeyboardMarkup();
        $inline = $this->inlineKeyboardMarkup();

        // Prioritize replyKeyboardMarkup if present
        if (! blank($reply)) {
            $keyboard = Keyboard::make();

            // Optional: include inline_keyboard too if it's set
            if (! blank($inline)) {
                $keyboard = Keyboard::make([
                    'inline_keyboard' => $inline,
                ]);
            }

            foreach ($reply as $row) {
                $keyboard->row($row);
            }

            return $keyboard;
        }

        // Fallback to inlineKeyboardMarkup only
        if (! blank($inline)) {
            return Keyboard::make([
                'inline_keyboard' => $inline,
            ]);
        }

        return [];
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

        $markup = $this->generateReplyMarkup();
        if (! blank($markup)) {
            $payload['reply_markup'] = $markup;
        }

        return Telegram::sendMessage($payload);
    }
}
