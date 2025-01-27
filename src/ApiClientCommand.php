<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class ApiClientCommand extends Command
{
    protected $signature = 'api:client {--regenerate : Allows to regenerate an existing client\'s key}';

    protected $description = 'Adds or updates api clients key';

    /**
     * @var class-string
     */
    protected string $model;

    public function __construct(private readonly Hasher $hasher)
    {
        parent::__construct();
        $this->model = config('api-key-auth.model');
    }

    public function handle(): void
    {
        if ($this->option('regenerate') === true) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    private function insert(): void
    {
        do {
            $key = $this->key();
            $name = $this->name();
            $ip = $this->ip();
            $this->displaySummary($name, $ip);
        } while (!$this->confirm('Is this information correct?'));
        if ($ip === '') {
            $ip = null;
        }
        $this->model::insert(['key' => $this->hasher->make($key), 'name' => $name, 'revoked' => false, 'allowed_ips' => $ip]);
        $this->info('Success!');
        $this->warn('Please copy your client\'s key: ' . $key);
    }

    private function update(): void
    {
        do {
            $key = $this->key();
            $id = $this->id();
            $this->info('Clients ID: ' . $id);
        } while (!$this->confirm('Is this information correct?'));
        $this->model::where('id', $id)->update(['key' => $this->hasher->make($key)]);
        $this->info('Success!');
        $this->warn('Please copy your client\'s key: ' . $key);
    }

    private function ip(): string
    {
        $this->comment('Example IP (IPV4 and/or IPV6) format - comma separated list: 127.0.0.1,684D:1111:222:3333:4444:5555:6:77,192.168.0.1');

        return $this->ask('Please enter API client\'s IP address (leave it blank for none)') ?? '';
    }

    private function displaySummary(string $name, string $ip): void
    {
        $this->comment('Entered information: ');
        $this->comment(' Client\'s name: ' . $name);
        if ($ip === '') {
            $this->comment(' IP Address: [NONE]');
        } else {
            $this->comment(' IP address: ' . $ip);
        }
    }

    private function name(): string
    {
        do {
            $name = $this->ask('Please enter API client\'s name');
        } while (empty($name));

        return $name;
    }

    private function id(): int
    {
        do {
            $id = $this->ask('Please enter client\'s id');
        } while (empty($id));

        return $id;
    }

    private function key(): string
    {
        return (string) Str::uuid();
    }
}
