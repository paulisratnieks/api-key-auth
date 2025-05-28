<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware;
use Tests\Support\Enums\Scope;
use Tests\Support\Routing\ApiClientRoute;

test('single route scopes are registered', function (): void {
    (new ApiClientRoute())()->scopes(Scope::ReadUsers);

    $this->asApiClient()
        ->getJson('/')
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
});

test('resource route scopes are registered', function (): void {
    Route::middleware(ApiClientMiddleware::class)
        ->resource('users', (new class {})::class)
        ->scopes('read:Users');

    $this->asApiClient()
        ->getJson('users')
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
});
