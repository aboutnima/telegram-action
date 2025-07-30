<?php

use Aboutnima\TelegramAction\Services\TelegramActionService;

beforeEach(function (): void {
    // Create `ZoomService` instance and request access-token
    $this->telegramAction = TelegramActionService::default();
});

it('`TelegramAction` facade is correctly bound and returns an instance of TelegramActionService', function (): void {
    expect($this->telegramAction)->toBeInstanceOf(TelegramActionService::class);
});
