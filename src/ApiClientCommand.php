<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class ApiClientCommand extends Command
{
    protected $signature = 'api:client {--regenerate : Allows to regenerate an existing client\'s key}';

    protected $description = 'Adds or updates api clients key';

    public function __construct(private readonly Hasher $hasher)
    {
        parent::__construct();
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
        $result = $this->confirmedAction(function (): array {
            $key = $this->key();
            $name = $this->name();
            $ip = $this->ip();
            $this->displaySummary($name, $ip);

            return compact('key', 'name', 'ip');
        });
        $this->model()::insert([
            'name' => $result['name'],
            'key' => $this->hasher->make($result['key']),
            'allowed_ips' => $result['ip'] === '' ? null : $result['ip'],
        ]);
        $this->displaySuccess($result['key']);
    }

    private function update(): void
    {
        $result = $this->confirmedAction(function (): array {
            $key = $this->key();
            $id = $this->id();

            return compact('key', 'id');
        });
        $this->model()::find($result['id'])
            ->update(['key' => $this->hasher->make($result['key'])]);
        $this->displaySuccess($result['key']);
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
        $this->comment(' IP address: ' . ($ip === '' ? '[NONE]' : $ip));
    }

    private function displaySuccess(string $key): void
    {
        $this->info('Success!');
        $this->warn('Please copy your client\'s key: ' . $key);
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

    private function confirmedAction(Closure $action): mixed
    {
        do {
            $result = $action();
        } while (!$this->confirm('Is this information correct?'));

        return $result;
    }

    /**
     * @return class-string
     */
    private function model(): string
    {
        return config('api-key-auth.model');
    }
}
