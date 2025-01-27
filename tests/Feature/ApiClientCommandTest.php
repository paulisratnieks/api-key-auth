<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Support\Models\ApiClient;

test('api client command creates model', function (): void {
    $name = fake()->word();
    $ips = fake()->ipv4();
    $key = fake()->uuid();
    Str::createUuidsUsing(fn (): string => $key);

    $this->artisan('api:client')
        ->expectsQuestion('Please enter API client\'s name', $name)
        ->expectsOutput('Example IP (IPV4 and/or IPV6) format - comma separated list: 127.0.0.1,684D:1111:222:3333:4444:5555:6:77,192.168.0.1')
        ->expectsQuestion('Please enter API client\'s IP address (leave it blank for none)', $ips)
        ->expectsOutput('Entered information: ')
        ->expectsOutput(' Client\'s name: ' . $name)
        ->expectsOutput(' IP address: ' . $ips)
        ->expectsQuestion('Is this information correct?', 'yes')
        ->expectsOutput('Success!')
        ->expectsOutputToContain('Please copy your client\'s key: ' . $key)
        ->assertOk();

    $this->assertEquals([
        'name' => $name,
        'allowed_ips' => $ips,
        'revoked' => false,
    ],ApiClient::firstOrFail()->only('name', 'revoked', 'allowed_ips'));
    $this->assertTrue(Hash::check($key, ApiClient::firstOrFail()->key));
});

test('api client command with regenerate flag updates model', function (): void {
    $client = ApiClient::factory()->create();
    $key = fake()->uuid();
    Str::createUuidsUsing(fn (): string => $key);

    $this->artisan('api:client', ['--regenerate' => true])
        ->expectsQuestion('Please enter client\'s id', $client->id)
        ->expectsOutput('Clients ID: ' . $client->id)
        ->expectsQuestion('Is this information correct?', 'yes')
        ->expectsOutput('Success!')
        ->expectsOutputToContain('Please copy your client\'s key: ' . $key)
        ->assertOk();

    $this->assertEquals([
        'name' => $client->name,
        'allowed_ips' => $client->allowed_ips,
        'revoked' => false,
    ],ApiClient::firstOrFail()->only('name', 'revoked', 'allowed_ips'));
    $this->assertTrue(Hash::check($key, ApiClient::firstOrFail()->key));
});
