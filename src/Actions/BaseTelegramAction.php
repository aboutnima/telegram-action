<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\TelegramActionInterface;
use Aboutnima\Telegram\Facades\TelegramAction;

abstract class BaseTelegramAction implements TelegramActionInterface
{
    private int $chatId;

    public function __construct()
    {
        $this->chatId = TelegramAction::getChatId();
    }

    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getChatId(): int {
        return $this->chatId;
    }

    /**
     * Override in child if message is needed.
     */
    public function message(): string|null
    {
        return null;
    }

    /**
     * Override in child if reply markup is needed.
     */
    public function replyMarkup(): array|null
    {
        return null;
    }

    /**
     * Executes the action by sending the message to Telegram.
     */
    public function handle(): void
    {
        $payload = [
            'chat_id' => $this->getChatId(),
            'text'    => $this->message(),
        ];

        if ($markup = $this->replyMarkup()) {
            $payload['reply_markup'] = $markup;
        }

        dd($payload);
    }
}
