<?php

namespace PaulisRatnieks\ApiKeyAuth\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @implements CastsAttributes<Collection, string>
 */
class AsList implements CastsAttributes
{
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): Collection {
        return str($value)
            ->explode(',')
            ->map(fn (string $ip): string => trim($ip));
    }

    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): string {
        return $value;
    }
}
