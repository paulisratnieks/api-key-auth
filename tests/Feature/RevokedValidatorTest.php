<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware;
use Tests\Support\Models\ApiClient;

beforeEach(function (): void {
    Route::get('/', fn (): JsonResponse => response()->json())
        ->middleware(ApiClientMiddleware::class);
});

test('revoked validator succeeds when api client is not revoked', function (): void {
    $this->withToken($this->createApiClient(), config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertOk();
});

test('revoked validator fails when api client is revoked', function (): void {
    $key = fake()->uuid();
    ApiClient::factory()
        ->key($key)
        ->revoked()
        ->create();

    $this->withToken($key, config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertUnauthorized();
});
