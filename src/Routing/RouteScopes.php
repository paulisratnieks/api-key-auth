<?php

declare(strict_types=1);

namespace PaulisRatnieks\ApiKeyAuth\Routing;

use BackedEnum;
use Closure;
use Illuminate\Routing\Route;

/**
 * @mixin Route
 */
class RouteScopes
{
    public function getScopes(): Closure
    {
        return function (): array {
            return $this->action['scopes'] ?? [];
        };
    }

    public function scopes(): Closure
    {
        return function (array|BackedEnum|string $scopes): static {
            $scopes = is_array($scopes) ? $scopes : func_get_args();
            $this->action['scopes'] = collect($scopes)->map(
                fn (BackedEnum|string $scope): string => $scope instanceof BackedEnum ? $scope->value : $scope,
            )->toArray();

            return $this;
        };
    }
}
