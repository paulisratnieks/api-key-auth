<?php

use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Support\Models\ApiClient;

test('api client update command with regenerate action regenerates key', function (): void {
    $clients = ApiClient::factory()
        ->count(2)
        ->state(new Sequence(
            ['revoked' => true],
            ['revoked' => false],
        ))
        ->create();

    $this->artisan('api-client:list')
        ->expectsTable(
            ['Id', 'Name', 'Allowed Ips', 'Revoked', 'Scopes'],
            $clients->map(fn (ApiClient $client): array => [
                ...collect($client->only('id', 'name'))->values()->toArray(),
                $client->allowed_ips->implode(','),
                $client->revoked ? 'true' : 'false',
                $client->scopes->implode(','),
            ]
            )->toArray(),
        )
        ->assertOk();
});
