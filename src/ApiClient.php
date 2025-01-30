<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    use HasFactory;

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
    ];
}
