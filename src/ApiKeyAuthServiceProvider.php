<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use PaulisRatnieks\ApiKeyAuth\Actions\ApiClientFetcher;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientListCommand;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientMakeCommand;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientUpdateCommand;
use PaulisRatnieks\ApiKeyAuth\Routing\PendingResourceRegistrationScopes;
use PaulisRatnieks\ApiKeyAuth\Routing\ResourceRegistrar;
use PaulisRatnieks\ApiKeyAuth\Routing\RouteScopes;

class ApiKeyAuthServiceProvider extends ServiceProvider
{
    public $bindings = [
        \Illuminate\Routing\ResourceRegistrar::class => ResourceRegistrar::class,
    ];

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/config/api-key-auth.php' => config_path('api-key-auth.php'),
        ], 'config');
        $this->publishesMigrations([
            dirname(__DIR__) . '/database/migrations' => database_path('migrations'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/api-key-auth.php', 'api-key-auth'
        );

        $this->app->when([ApiClientFetcher::class, ApiClientMakeCommand::class, ApiClientUpdateCommand::class])
            ->needs(Hasher::class)
            ->give(ShaHasher::class);

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
        $this->registerMacros();
    }

    protected function registerMacros(): void
    {
        Route::mixin(new RouteScopes());
        PendingResourceRegistration::mixin(new PendingResourceRegistrationScopes());
    }

    protected function registerCommands(): void
    {
        $this->commands([
            ApiClientMakeCommand::class,
            ApiClientUpdateCommand::class,
            ApiClientListCommand::class,
        ]);
    }
}
