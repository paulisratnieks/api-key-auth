<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Contracts\Hashing\Hasher;
use SensitiveParameter;

class SHAHasher implements Hasher
{
    public function info($hashedValue): array
    {
        return [];
    }

    public function make(#[SensitiveParameter] $value, array $options = []): string
    {
        return hash('sha256', $value . config('app.key'));
    }

    public function check(#[SensitiveParameter] $value, $hashedValue, array $options = []): bool
    {
        return $this->make($value, $options) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        return false;
    }
}
