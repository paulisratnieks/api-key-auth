<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands;

use BackedEnum;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use PaulisRatnieks\ApiKeyAuth\Commands\Enums\UpdateAction;

class ApiClientUpdateCommand extends Command
{
    protected $signature = 'api-client:update';

    protected $description = 'Update an API client';

    public function __construct(private readonly Hasher $hasher)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $action = $this->choice(
            'What update action would you like to use?',
            collect(UpdateAction::cases())
                ->map(fn (BackedEnum $action) => $action->value)
                ->toArray(),
        );

        do {
            try {
                $id = $this->ask('Please enter API client\'s id');
                $client = config('api-key-auth.model')::findOrFail($id);
            } catch (ModelNotFoundException) {
                $this->error('API client with id=' . $id . ' not found');
            }
        } while (empty($client));

        $client->updateOrFail(
            match ($action) {
                UpdateAction::Regenerate->value => $this->regenerate(),
                UpdateAction::Revoke->value => ['revoked' => true],
                UpdateAction::RemoveRevoke->value => ['revoked' => false],
                default => throw new Exception('Unsupported action: ' . $action)
            }
        );
        $this->info('API client updated successfully.');
    }

    private function regenerate(): array
    {
        $key = (string) Str::uuid();
        $this->warn('Please copy API client\'s key: ' . $key);

        return [
            'key' => $this->hasher->make($key),
        ];
    }
}
