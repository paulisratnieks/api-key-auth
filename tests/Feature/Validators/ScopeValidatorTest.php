<?php

use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Models\ApiClient;
use Tests\Support\Routing\ApiClientRoute;

test('scope validator returns correct response when scoped route', function (
    array|BackedEnum|string $routeScopes,
    string $clientScopes,
    bool $isSuccessful
): void {
    (new ApiClientRoute())()->scopes($routeScopes);

    $this->asApiClient(ApiClient::factory()->state(['scopes' => $clientScopes]))
        ->getJson('/')
        ->assertStatus($isSuccessful ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED);
})->with([
    'single overlapping' => ['read:Users', 'read:Users', true],
    'single not overlapping' => ['read:Users', '', false],
    'multiple overlapping' => [['read:Users', 'write:Users'], 'read:Users,write:Users,read:Articles', true],
    'multiple party overlapping' => [['read:Users', 'read:Articles'], 'read:Users', false],
]);
