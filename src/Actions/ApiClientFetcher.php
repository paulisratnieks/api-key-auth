<?php

namespace PaulisRatnieks\ApiKeyAuth\Actions;

use Closure;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ApiClientFetcher
{
    public function __construct(private readonly Hasher $hasher) {}

    public function handle(Request $request, Closure $next): mixed
    {
        return $next(
            config('api-key-auth.model')::where('key', $this->apiKey($request))
                ->firstOrFail()
        );
    }

    private function apiKey(Request $request): string
    {
        return $this->hasher->make(
            str($request->header(config('api-key-auth.header')))
                ->replace(config('api-key-auth.header_key'), '')
                ->trim()
                ->whenEmpty(fn () => throw new BadRequestException('No authorization token given.'))
                ->toString()
        );
    }
}
