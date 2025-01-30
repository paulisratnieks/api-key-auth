<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\ServiceProvider;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientListCommand;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientMakeCommand;
use PaulisRatnieks\ApiKeyAuth\Commands\ApiClientUpdateCommand;

class ApiKeyAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/config/api-key-auth.php' => config_path('api-key-auth.php'),
        ], 'config');
        $this->publishesMigrations([
            dirname(__DIR__) . '/database/migrations' => database_path('migrations'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        $this->app->when([ApiClientFetcher::class, ApiClientMakeCommand::class, ApiClientUpdateCommand::class])
            ->needs(Hasher::class)
            ->give(ShaHasher::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/api-key-auth.php', 'api-key-auth'
        );
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
