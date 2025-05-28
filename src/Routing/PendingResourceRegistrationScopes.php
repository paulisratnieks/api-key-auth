<?php

declare(strict_types=1);

namespace PaulisRatnieks\ApiKeyAuth\Routing;

use BackedEnum;
use Closure;
use Illuminate\Routing\PendingResourceRegistration;

/**
 * @mixin PendingResourceRegistration
 */
class PendingResourceRegistrationScopes
{
    public function scopes(): Closure
    {
        return function (array|BackedEnum|string $scopes): static {
            $scopes = is_array($scopes) ? $scopes : func_get_args();
            $this->options['scopes'] = collect($scopes)->map(
                fn (BackedEnum|string $scope): int|string => $scope instanceof BackedEnum ? $scope->value : $scope,
            )->toArray();

            return $this;
        };
    }
}
