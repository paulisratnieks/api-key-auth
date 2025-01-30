<?php

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Override;
use Tests\Support\Factories\ApiClientFactory;

class ApiClient extends \PaulisRatnieks\ApiKeyAuth\ApiClient
{
    use HasFactory;

    #[Override]
    protected static function newFactory(): ApiClientFactory
    {
        return ApiClientFactory::new();
    }
}
