<?php

use Tests\Support\Models\ApiClient;
use Tests\Support\Routing\ApiClientRoute;

beforeEach(function (): void {
    (new ApiClientRoute())();
});

test('revoked validator succeeds when api client is not revoked', function (): void {
    $this->asApiClient()
        ->getJson('/')
        ->assertOk();
});

test('revoked validator fails when api client is revoked', function (): void {
    $this->asApiClient(ApiClient::factory()->revoked())
        ->getJson('/')
        ->assertUnauthorized();
});
