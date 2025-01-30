<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Support\Models\ApiClient;

test('api client make command creates api client', function (): void {
    $name = fake()->word();
    $ips = fake()->ipv4();
    $key = fake()->uuid();
    Str::createUuidsUsing(fn (): string => $key);

    $this->artisan('api-client:make')
        ->expectsQuestion('Please enter API client\'s name', $name)
        ->expectsOutput('Example IP (IPV4 and/or IPV6) format - comma separated list: 127.0.0.1,684D:1111:222:3333:4444:5555:6:77,192.168.0.1')
        ->expectsQuestion('Please enter API client\'s IP address (leave it blank for none)', $ips)
        ->expectsOutput('Entered information: ')
        ->expectsOutput(' Client\'s name: ' . $name)
        ->expectsOutput(' IP address: ' . $ips)
        ->expectsQuestion('Is this information correct?', 'yes')
        ->expectsOutputToContain('Please copy API client\'s key: ' . $key)
        ->expectsOutput('API client created successfully.')
        ->assertOk();

    $client = ApiClient::firstOrFail();
    $this->assertEquals([
        'name' => $name,
        'allowed_ips' => $ips,
        'revoked' => false,
    ],$client->only('name', 'revoked', 'allowed_ips'));
    $this->assertTrue(Hash::check($key, $client->key));
});
