<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class ApiClientMakeCommand extends Command
{
    protected $signature = 'api-client:make';

    protected $description = 'Create an API client';

    public function __construct(private readonly Hasher $hasher)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        do {
            $key = (string) Str::uuid();

            $name = $this->ask('Please enter API client\'s name');

            $this->comment('Example IP (IPV4 and/or IPV6) format - comma separated list: 127.0.0.1,684D:1111:222:3333:4444:5555:6:77,192.168.0.1');
            $ip = $this->ask('Please enter API client\'s IP address (leave it blank for none)') ?? '';

            $this->comment('Entered information: ');
            $this->comment(' Client\'s name: ' . $name);
            $this->comment(' IP address: ' . ($ip === '' ? '[NONE]' : $ip));
        } while (!$this->confirm('Is this information correct?'));

        config('api-key-auth.model')::insert([
            'name' => $name,
            'key' => $this->hasher->make($key),
            'allowed_ips' => empty($ip) ? null : $ip,
        ]);
        $this->warn('Please copy API client\'s key: ' . $key);
        $this->info('API client created successfully.');
    }
}
