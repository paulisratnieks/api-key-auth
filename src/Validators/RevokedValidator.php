<?php

namespace PaulisRatnieks\ApiKeyAuth\Validators;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

class RevokedValidator implements Validator
{
    /**
     * @throws AuthenticationException
     */
    public function handle(Model $client, Closure $next): mixed
    {
        if ($client->revoked) {
            throw new AuthenticationException('You are not authorized to execute this action.');
        }

        return $next($client);
    }
}
