<?php

use Aboutnima\LaravelZoom\Exceptions\ZoomException;
use Aboutnima\LaravelZoom\Facades\Zoom;
use Aboutnima\LaravelZoom\Services\TelegramActionService;

beforeEach(function (): void {
    // Create `ZoomService` instance and request access-token
    $this->zoom = Zoom::default();
});

it('`Zoom` facade is correctly bound and returns an instance of ZoomService', function (): void {
    expect($this->zoom)->toBeInstanceOf(TelegramActionService::class);
});

it('`sendRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'sendRequest'))->toBeTrue();
});

it('`getRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'getRequest'))->toBeTrue();
});

it('`postRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'postRequest'))->toBeTrue();
});

it('`patchRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'patchRequest'))->toBeTrue();
});

it('`putRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'putRequest'))->toBeTrue();
});

it('`deleteRequest` method exists', function (): void {
    expect(method_exists($this->zoom, 'deleteRequest'))->toBeTrue();
});

it('can call `users/me` endpoint via `getRequest` method and receive 200 OK status', function (): void {
    $endpoint = 'users/me';

    $response = $this->zoom->getRequest($endpoint);

    $fakeRequest = Http::fake()->get($this->zoom->tokenManager()->getApiUrl().$endpoint);

    // Just ensure the request didn't throw and returned an array
    expect($response->json())->toBeArray()
        ->not->toBeEmpty()
        ->and($response->status())->toBe($fakeRequest->status());
});

// it('throws `RuntimeException` when Zoom request fails', function (): void {
//    expect(
//        fn () => $this->zoom->sendRequest('get', '!@#')
//    )->toThrow(ZoomException::class);
// });
