<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Support\Models\ApiClient;

test('api client make command creates api client', function (): void {
    $name = fake()->word();
    $ip = fake()->word() . ',' . fake()->word();
    $key = fake()->uuid();
    $scope = fake()->word() . ',' . fake()->word();
    Str::createUuidsUsing(fn (): string => $key);

    $this->artisan('api-client:make')
        ->expectsQuestion('Enter API client\'s name', $name)
        ->expectsQuestion('Enter API client\'s IP address (Ex: 127.0.0.1,192.168.0.1) (empty allowed)', $ip)
        ->expectsQuestion('Enter API client\'s scopes (Ex: read:Users,write:Users) (empty allowed)', $scope)
        ->expectsOutput('Entered information: ')
        ->expectsOutput('Name: ' . $name)
        ->expectsOutput('Allowed Ips: ' . $ip)
        ->expectsOutput('Scopes: ' . $scope)
        ->expectsQuestion('Is this information correct?', 'yes')
        ->expectsOutputToContain('Please copy API client\'s key: ' . $key)
        ->expectsOutput('API client created successfully.')
        ->assertOk();

    $client = ApiClient::firstOrFail();
    $this->assertEquals([
        'name' => $name,
        'allowed_ips' => str($ip)->explode(','),
        'scopes' => str($scope)->explode(','),
        'revoked' => false,
    ],$client->only('name', 'revoked', 'allowed_ips', 'scopes'));
    $this->assertTrue(Hash::check($key, $client->key));
});
