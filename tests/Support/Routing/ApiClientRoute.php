<?php

namespace Tests\Support\Routing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware;

class ApiClientRoute
{
    public function __invoke(): \Illuminate\Routing\Route
    {
        return Route::get('/', fn (): JsonResponse => response()->json())
            ->middleware(ApiClientMiddleware::class);
    }
}
