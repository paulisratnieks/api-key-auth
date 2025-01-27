<?php

namespace PaulisRatnieks\ApiKeyAuth\Validators;

use Closure;
use Illuminate\Database\Eloquent\Model;

interface Validator
{
    public function handle(Model $client, Closure $next): mixed;
}
