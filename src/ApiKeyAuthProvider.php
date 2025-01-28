<?php

namespace PaulisRatnieks\ApiKeyAuth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\ServiceProvider;

class ApiKeyAuthProvider extends ServiceProvider
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
            $this->commands([
                ApiClientCommand::class,
            ]);
        }

        $this->app->when([ApiClientFetcher::class, ApiClientCommand::class])
            ->needs(Hasher::class)
            ->give(ShaHasher::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/api-key-auth.php', 'api-key-auth'
        );
    }
}
