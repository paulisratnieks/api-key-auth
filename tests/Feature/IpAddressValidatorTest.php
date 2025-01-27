<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware;
use Tests\Support\Models\ApiClient;

beforeEach(function (): void {
    Route::get('/', fn (): JsonResponse => response()->json())
        ->middleware(ApiClientMiddleware::class);
});

test('ip address validator succeeds when request ip in whitelist', function (): void {
    $this->withToken($this->createApiClient(), config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertOk();
});

test('ip address validator fails when request ip not in whitelist', function (): void {
    $key = fake()->uuid();
    ApiClient::factory()
        ->key($key)
        ->state([
            'allowed_ips' => fake()->ipv4(),
        ])
        ->create();

    $this->withToken($key, config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertUnauthorized();
});
