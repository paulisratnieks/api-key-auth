<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Database\Eloquent\Model;
use PaulisRatnieks\ApiKeyAuth\Casts\AsList;

class ApiClient extends Model
{
    /** @var list<string> */
    public $guarded = [
        'id',
    ];

    /** @var list<string> */
    protected $hidden = [
        'key',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'revoked' => 'boolean',
        'scopes' => AsList::class,
        'allowed_ips' => AsList::class,
    ];
}
