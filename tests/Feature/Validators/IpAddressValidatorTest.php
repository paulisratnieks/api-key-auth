<?php

use Tests\Support\Models\ApiClient;
use Tests\Support\Routing\ApiClientRoute;

beforeEach(function (): void {
    (new ApiClientRoute())();
});

test('ip address validator succeeds when request ip in whitelist', function (): void {
    $this->asApiClient()
        ->getJson('/')
        ->assertOk();
});

test('ip address validator fails when request ip not in whitelist', function (): void {
    $key = fake()->uuid();
    ApiClient::factory()
        ->key($key)
        ->state([
            'allowed_ips' => fake()->ipv4() . ',' . fake()->ipv4(),
        ])
        ->create();

    $this->withToken($key, config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertUnauthorized();
});
