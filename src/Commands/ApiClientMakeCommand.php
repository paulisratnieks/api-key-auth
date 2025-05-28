<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class ApiClientMakeCommand extends Command
{
    protected $signature = 'api-client:make';

    protected $description = 'Create an API client';

    protected array $columnPrompts = [
        'name' => [
            'text' => 'Enter API client\'s name',
            'required' => true,
        ],
        'allowed_ips' => [
            'text' => 'Enter API client\'s IP address (Ex: 127.0.0.1,192.168.0.1)',
            'required' => false,
        ],
        'scopes' => [
            'text' => 'Enter API client\'s scopes (Ex: read:Users,write:Users)',
            'required' => false,
        ],
    ];

    public function handle(Hasher $hasher): void
    {
        do {
            $key = (string) Str::uuid();

            $inputs = collect($this->columnPrompts)->mapWithKeys(function (array $prompt, string $column): array {
                $text = str($prompt['text']);
                if ($prompt['required'] === false) {
                    $text = $text->append(' (empty allowed)');
                }

                do {
                    $input = $this->ask($text) ?? '';
                } while ($prompt['required'] === true && empty($input));

                return [$column => $input];
            });

            $this->comment('Entered information: ');
            $inputs->each(function (string $input, string $column): void {
                $this->comment(
                    str($column)->headline()
                        ->append(': ', empty($input) ? '[None]' : $input),
                );
            });
        } while (!$this->confirm('Is this information correct?'));

        config('api-key-auth.model')::create([
            'key' => $hasher->make($key),
            ...$inputs->mapWithKeys(fn (string $input, string $column) => [$column => empty($input) ? null : $input]),
        ]);

        $this->warn('Please copy API client\'s key: ' . $key);
        $this->info('API client created successfully.');
    }
}
