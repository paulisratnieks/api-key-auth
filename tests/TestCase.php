<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Override;
use PaulisRatnieks\ApiKeyAuth\ApiKeyAuthProvider;
use PaulisRatnieks\ApiKeyAuth\SHAHasher;
use Tests\Support\Models\ApiClient;

abstract class TestCase extends Orchestra
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        (require __DIR__ . '/../database/migrations/create_api_clients_table.php.stub')
            ->up();

        $this->app->bind('hash', SHAHasher::class);
    }

    public function createApiClient(string $model = ApiClient::class): string
    {
        $key = fake()->uuid();
        $model::factory()
            ->key($key)
            ->create();

        return $key;
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
            ApiKeyAuthProvider::class,
        ];
    }
}
