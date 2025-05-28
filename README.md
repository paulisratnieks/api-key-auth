# Simple API client generation and authentication
`api-key-auth` is a highly customizable Laravel package for API client management, authentication and authorization. The package contains a single middleware `ApiClientMiddleware` and a few artisan commands to manage the clients.

## Installation
You can install the package via composer:

``` bash
composer require paulisratnieks/api-key-auth
```

You should publish the config file `config/api-key-auth.php` and run the migrations to create the `api_clients` table:

```bash
php artisan vendor:publish --provider="PaulisRatnieks\ApiKeyAuth\ApiKeyAuthServiceProvider"
php artisan migrate
```

## Usage

### Managing API clients
The package contains multiple artisan commands for API client management: `api-client:make`, `api-client:list`, `api-client:update`.

To create a new client:
``` bash
php artisan api-client:make
```
The command will prompt for multiple attributes that are necessary for the `ApiClient` model. After creation, the command will output the API key in plain text, which should be saved and used to authenticate the API client. The API client has to send an HTTP authorization header with the generated API key `Authorization: ApiKey {api-key}`.

All the API clients can be viewed with the `api-client:list` command, and they can be managed with the `api-client:update` command which supports the following actions: `regenerate`, `revoke`, `undo-revoke`. 

### Adding the middleware
In `config/app.php` (Laravel 11 and newer) you should add the middleware to the global middleware stack:

```php
->withMiddleware(function (Middleware $middleware) {
     $middleware->append(\PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware::class);
})
```
The middleware will validate each request against the HTTP header containing the API key.

## Customization
These are the contents of the published config file `config/api-key-auth.php`: 

```php
return [
    // This is the model used by the ApiClientMiddleware.
    'model' => PaulisRatnieks\ApiKeyAuth\ApiClient::class,

    /*
     * These are all the validators used by the ApiClientMiddleware. You can add or remove any classes
     * that implement the PaulisRatnieks\ApiKeyAuth\Validators\Validator interface.
     */
    'validators' => [
        PaulisRatnieks\ApiKeyAuth\Validators\IpAddressValidator::class,
        PaulisRatnieks\ApiKeyAuth\Validators\RevokedValidator::class,
        PaulisRatnieks\ApiKeyAuth\Validators\ScopeValidator::class,
    ],

    // The name of the http header that will be used for authentication.
    'header' => 'Authorization',

    // The key that will hold the authorization header's api key.
    'header_key' => 'ApiKey',
];
```
It is possible to configure:
* which model is used by the middleware using the `model` config entry
* which validators the middleware uses by the `validators` entry
* the HTTP header format using `header` amd `header_key` entries. The HTTP header sent by the API clients should follow this format: `{header}: {header_key} {api-key}`.