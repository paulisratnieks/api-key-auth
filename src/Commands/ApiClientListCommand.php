<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ApiClientListCommand extends Command
{
    protected $signature = 'api-client:list';

    protected $description = 'Display a list of all registered API clients';

    public function handle(): void
    {
        $this->table(
            ['ID', 'Name', 'Allowed IP\'s', 'Revoked'],
            $this->rows()
        );
    }

    private function rows(): array
    {
        /**
         * @var class-string $modelClass
         */
        $modelClass = config('api-key-auth.model');

        return $modelClass::all(['id', 'name', 'allowed_ips', 'revoked'])
            ->map(fn (Model $apiClient): array => [
                ...$apiClient->toArray(),
                'revoked' => $apiClient->revoked ? 'true' : 'false',
            ])
            ->toArray();
    }
}
