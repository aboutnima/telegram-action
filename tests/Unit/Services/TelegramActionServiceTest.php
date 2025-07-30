<?php

use Aboutnima\Telegram\Services\TelegramActionService;

beforeEach(function (): void {
    // Create `TelegramActionService` instance
    $this->telegramAction = (new TelegramActionService)->default();
});

it('`TelegramAction` facade is correctly bound and returns an instance of TelegramActionService', function (): void {
    expect($this->telegramAction)->toBeInstanceOf(TelegramActionService::class);
});
