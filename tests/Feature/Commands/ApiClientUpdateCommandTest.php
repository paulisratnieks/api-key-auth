<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PaulisRatnieks\ApiKeyAuth\Commands\Enums\UpdateAction;
use Tests\Support\Models\ApiClient;

test('api client update command with regenerate action regenerates key', function (): void {
    $client = ApiClient::factory()->create();
    $key = fake()->uuid();
    Str::createUuidsUsing(fn (): string => $key);

    $this->artisan('api-client:update')
        ->expectsQuestion('What update action would you like to use?', UpdateAction::Regenerate->value)
        ->expectsQuestion('Please enter API client\'s id', $client->id)
        ->expectsOutputToContain('Please copy API client\'s key: ' . $key)
        ->expectsOutput('API client updated successfully.')
        ->assertOk();

    $updatedClient = ApiClient::firstOrFail();
    $this->assertNotEquals($client->key, $updatedClient->key);
    $this->assertTrue(Hash::check($key, $updatedClient->key));
});

test('api client update command with revoke action revokes model', function (): void {
    $client = ApiClient::factory()->create();
    $this->assertFalse($client->revoked);

    $this->artisan('api-client:update')
        ->expectsQuestion('What update action would you like to use?', UpdateAction::Revoke->value)
        ->expectsQuestion('Please enter API client\'s id', $client->id)
        ->expectsOutput('API client updated successfully.')
        ->assertOk();

    $this->assertTrue(ApiClient::firstOrFail()->revoked);
});

test('api client update command with remove revoke action removes revoke on model', function (): void {
    $client = ApiClient::factory()
        ->revoked()
        ->create();
    $this->assertTrue($client->revoked);

    $this->artisan('api-client:update')
        ->expectsQuestion('What update action would you like to use?', UpdateAction::RemoveRevoke->value)
        ->expectsQuestion('Please enter API client\'s id', $client->id)
        ->expectsOutput('API client updated successfully.')
        ->assertOk();

    $this->assertFalse(ApiClient::firstOrFail()->revoked);
});
