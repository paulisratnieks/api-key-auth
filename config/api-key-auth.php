<?php

use PaulisRatnieks\ApiKeyAuth\ApiClient;
use PaulisRatnieks\ApiKeyAuth\Validators\IpAddressValidator;
use PaulisRatnieks\ApiKeyAuth\Validators\RevokedValidator;

return [
    'model' => ApiClient::class,

    'validators' => [
        IpAddressValidator::class,
        RevokedValidator::class,
    ],

    // The name of the http header that will be used for authentication
    'header' => 'Authorization',

    // The key that will hold the authorization header's api key
    'header_key' => 'ApiKey',
];
