<?php

namespace PaulisRatnieks\ApiKeyAuth\Validators;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

class IpAddressValidator implements Validator
{
    /**
     * @throws AuthenticationException
     */
    public function handle(Model $client, Closure $next): mixed
    {
        if (!empty($client->allowed_ips)
            && !str($client->allowed_ips)
                ->explode(',')
                ->contains(request()->ip())
        ) {
            throw new AuthenticationException('You are not authorized to execute this action.');
        }

        return $next($client);
    }
}
