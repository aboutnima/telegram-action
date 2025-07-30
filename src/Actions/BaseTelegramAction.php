<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\TelegramActionInterface;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Base class for all Telegram actions.
 * Provides default implementations for message sending settings.
 */
abstract class BaseTelegramAction implements TelegramActionInterface
{
    /**
     * Telegram chat ID to send the message to.
     */
    private int $chatId;

    /**
     * Indicates whether the message sent by this action should be deleted
     * before the next action is executed
     */
    public bool $deleteOnNextAction = false;

    /**
     * Set the chat ID.
     */
    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * Get the current chat ID.
     */
    public function getChatId(): int
    {
        return $this->chatId;
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
        if (blank($markup) || (is_array($markup) && count($markup) === 0)) {
            $payload['reply_markup'] = $markup;
        }

        return Telegram::sendMessage($payload);
    }
}
