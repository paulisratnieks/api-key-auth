<?php

declare(strict_types=1);

namespace PaulisRatnieks\ApiKeyAuth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class ApiClientMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        app(Pipeline::class)
            ->send($request)
            ->through([
                ApiClientFetcher::class,
                ...config('api-key-auth.validators'),
            ])
            ->thenReturn();

        return $next($request);
    }
}
