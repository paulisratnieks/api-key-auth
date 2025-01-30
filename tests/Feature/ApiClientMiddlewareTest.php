<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Route;
use Mockery\MockInterface;
use PaulisRatnieks\ApiKeyAuth\ApiClientMiddleware;
use PaulisRatnieks\ApiKeyAuth\Validators\RevokedValidator;
use PaulisRatnieks\ApiKeyAuth\Validators\Validator;
use Tests\Support\Models\CustomApiClient;

beforeEach(function (): void {
    Route::get('/', fn (): JsonResponse => response()->json())
        ->middleware(ApiClientMiddleware::class);
});

test('api client middleware throws bad request with no authorization header', function (): void {
    $this->get('/')
        ->assertBadRequest();
});

test('api client middleware throws not found with non existent client', function (): void {
    $this->withToken(fake()->uuid(), config('api-key-auth.header_key'))
       ->getJson('/')
       ->assertNotFound();
});

test('api client middleware passes with existing api client', function (): void {
    $this->withToken($this->createApiClient(), config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertOk();
});

test('api client uses configured authorization header values', function (): void {
    config([
        'api-key-auth.header' => 'CustomHeaderName',
        'api-key-auth.header_key' => 'CustomHeaderKey',
    ]);

    $this->withHeaders([config('api-key-auth.header') => config('api-key-auth.header_key') . ' ' . $this->createApiClient()])
        ->getJson('/')
        ->assertOk();
});

test('api client middleware uses configured model', function (): void {
    config(['api-key-auth.model' => CustomApiClient::class]);
    $this->partialMock(RevokedValidator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('handle')
            ->once()
            ->withArgs(function (Model $model): bool {
                return $model instanceof CustomApiClient;
            });
    });

    $this->withToken($this->createApiClient(CustomApiClient::class), config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertOk();
});

test('api client middleware uses configured validators', function (): void {
    Exceptions::fake();
    config(['api-key-auth.validators' => [
        new class implements Validator {
            public function handle(Model $client, Closure $next): mixed
            {
                throw new Exception('Validation failed');
            }
        },
    ]]);

    $this->withToken($this->createApiClient(), config('api-key-auth.header_key'))
        ->getJson('/')
        ->assertInternalServerError();

    Exceptions::assertReported(fn (Exception $e): bool => $e->getMessage() === 'Validation failed');
});
