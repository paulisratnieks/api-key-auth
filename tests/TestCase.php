<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Override;
use PaulisRatnieks\ApiKeyAuth\ApiKeyAuthServiceProvider;
use PaulisRatnieks\ApiKeyAuth\ShaHasher;
use Tests\Support\Models\ApiClient;

abstract class TestCase extends Orchestra
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        (require dirname(__DIR__) . '/database/migrations/2025_01_30_000001_create_api_clients_table.php')
            ->up();

        $this->app->bind('hash', ShaHasher::class);
    }

    /**
     * @param null|Factory<ApiClient> $factory
     */
    public function apiClientKey(?Factory $factory = null): string
    {
        $key = fake()->uuid();
        ($factory ?? ApiClient::factory())
            ->key($key)
            ->create();

        return $key;
    }

    /**
     * @param null|Factory<ApiClient> $factory
     */
    public function asApiClient(?Factory $factory = null): TestCase
    {
        return $this->withToken(
            $this->apiClientKey($factory),
            config('api-key-auth.header_key')
        );
    }

    /**
     * @param Application $app
     */
    #[Override]
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    #[Override]
    protected function getPackageProviders($app): array
    {
        return [
            ApiKeyAuthServiceProvider::class,
        ];
    }
}
