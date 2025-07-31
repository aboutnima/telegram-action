<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\BaseTelegramActionInterface;
use Aboutnima\Telegram\Facades\TelegramAction;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Base class for all Telegram actions.
 * Provides structure for setting message, keyboard, and payload logic.
 */
abstract class BaseTelegramAction implements BaseTelegramActionInterface
{
    /**
     * The unique identifier key for this action.
     */
    protected string $key = '';

    /**
     * Indicates whether the message sent by this action should be deleted
     * before the next action is executed.
     */
    protected bool $deleteOnNextAction = false;

    /**
     * Used when setting payload for the next action via withPayload().
     */
    protected ?string $nextActionKeyNameWithPayload = null;

    /**
     * Payload data received from a previous action.
     */
    private array $payload = [];

    /**
     * Message text to send to the user.
     */
    protected string $message = '';

    /**
     * Inline keyboard markup structure.
     */
    protected array $inlineKeyboardMarkup = [];

    /**
     * Reply keyboard markup structure.
     */
    protected array $replyKeyboardMarkup = [];

    /**
     * Resolve an instance of this action via the service container.
     */
    public static function make(): self
    {
        return app(static::class);
    }

    /**
     * Get the action key, including payload reference if set.
     */
    public function getKey(): string
    {
        return $this->nextActionKeyNameWithPayload ?? $this->key;
    }

    /**
     * Set payload to be sent to the next action and generate a unique key.
     */
    public function withPayload(array $payload): self
    {
        $newKeyName = TelegramAction::putPayloadCache($this->key, $payload);
        $this->nextActionKeyNameWithPayload = $newKeyName;

        return $this;
    }

    /**
     * Set the payload data for this action.
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Get the payload data for this action.
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Get the current Telegram chat ID.
     */
    public function getChatId(): int
    {
        return TelegramAction::getChatId();
    }

    /**
     * Get whether the message should be deleted before the next action.
     */
    public function getDeleteOnNextAction(): bool
    {
        return $this->deleteOnNextAction;
    }

    /**
     * Get the message text that will be sent.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the inline keyboard markup array.
     */
    public function getInlineKeyboardMarkup(): array
    {
        return $this->inlineKeyboardMarkup;
    }

    /**
     * Get the reply keyboard markup array.
     */
    public function getReplyKeyboardMarkup(): array
    {
        return $this->replyKeyboardMarkup;
    }

    /**
     * Prepare action logic before sending message (e.g. set message or keyboard).
     * Override this in child classes.
     */
    public function prepare(): void
    {
        // To be implemented in child actions
    }

    /**
     * Generate the Telegram reply markup (keyboard).
     * Prefers reply markup if set, otherwise falls back to inline keyboard.
     */
    public function generateReplyMarkup(): mixed
    {
        $reply = $this->getReplyKeyboardMarkup();
        $inline = $this->getInlineKeyboardMarkup();

        if (!blank($reply)) {
            $keyboard = Keyboard::make();

            if (!blank($inline)) {
                $keyboard = Keyboard::make([
                    'inline_keyboard' => $inline,
                ]);
            }

            foreach ($reply as $row) {
                $keyboard->row($row);
            }

            return $keyboard;
        }

        if (!blank($inline)) {
            return Keyboard::make([
                'inline_keyboard' => $inline,
            ]);
        }

        return [];
    }

    /**
     * Handle the action execution: prepare, compose message, and send it.
     */
    public function handle(): mixed
    {
        $this->prepare();

        $payload = [
            'chat_id' => $this->getChatId(),
            'text' => $this->getMessage(),
        ];

        $markup = $this->generateReplyMarkup();
        if (!blank($markup)) {
            $payload['reply_markup'] = $markup;
        }

        return Telegram::sendMessage($payload);
    }
}
