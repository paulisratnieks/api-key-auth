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
            ['ID', 'Name', 'Allowed IP\'s', 'Revoked'],
            $clients->map(fn (ApiClient $client): array => [
                ...collect($client->only('id', 'name', 'allowed_ips'))
                    ->values()
                    ->toArray(),
                $client->revoked ? 'true' : 'false',
            ]
            )->toArray(),
        )
        ->assertOk();
});
