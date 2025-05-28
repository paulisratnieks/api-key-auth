<?php

namespace PaulisRatnieks\ApiKeyAuth\Validators;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

class ScopeValidator implements Validator
{
    /**
     * @throws AuthenticationException
     */
    public function handle(Model $client, Closure $next): mixed
    {
        if (collect(request()->route()->getScopes())
                ->diff($client->scopes)
                ->isNotEmpty()
        ) {
            throw new AuthenticationException('You are not authorized to execute this action.');
        }

        return $next($client);
    }
}
