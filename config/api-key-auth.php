<?php

use PaulisRatnieks\ApiKeyAuth\ApiClient;
use PaulisRatnieks\ApiKeyAuth\Validators\IpAddressValidator;
use PaulisRatnieks\ApiKeyAuth\Validators\RevokedValidator;
use PaulisRatnieks\ApiKeyAuth\Validators\ScopeValidator;

return [
    // This is the model used by the ApiClientMiddleware.
    'model' => ApiClient::class,

    /*
     * These are all the validators used by the ApiClientMiddleware. You can add or remove any classes
     * that implement the PaulisRatnieks\ApiKeyAuth\Validators\Validator interface.
     */
    'validators' => [
        IpAddressValidator::class,
        RevokedValidator::class,
        ScopeValidator::class,
    ],

    // The name of the http header that will be used for authorization.
    'header' => 'Authorization',

    // The key that will hold the authorization header's api key.
    'header_key' => 'ApiKey',
];
