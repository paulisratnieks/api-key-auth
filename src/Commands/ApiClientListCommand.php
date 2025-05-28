<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ApiClientListCommand extends Command
{
    protected $signature = 'api-client:list';

    protected $description = 'Display a list of all registered API clients';

    /**
     * @var list<string>
     */
    protected array $columns = [
        'id',
        'name',
        'allowed_ips',
        'revoked',
        'scopes',
    ];

    public function handle(): void
    {
        $this->table(
            collect($this->columns)
                ->map(fn (string $column): string => str($column)->headline())
                ->toArray(),
            $this->rows()
        );
    }

    protected function rows(): array
    {
        /**
         * @var class-string $modelClass
         */
        $modelClass = config('api-key-auth.model');

        return $modelClass::all($this->columns)
            ->map(fn (Model $client): array => $this->serialized($client))
            ->toArray();
    }

    protected function serialized(Model $client): array
    {
        return [
            ...$client->toArray(),
            'allowed_ips' => $client->allowed_ips->implode(','),
            'scopes' => $client->scopes->implode(','),
            'revoked' => $client->revoked ? 'true' : 'false',
        ];
    }
}
