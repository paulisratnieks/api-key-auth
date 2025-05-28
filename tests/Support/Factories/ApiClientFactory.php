<?php

namespace Tests\Support\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Models\ApiClient;

class ApiClientFactory extends Factory
{
    protected $model = ApiClient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => Hash::make(fake()->uuid()),
            'name' => fake()->word(),
            'allowed_ips' => '127.0.0.1',
            'revoked' => false,
            'scopes' => '',
        ];
    }

    public function revoked(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'revoked' => true,
            ];
        });
    }

    public function key(string $key): Factory
    {
        return $this->state(function (array $attributes) use ($key) {
            return [
                'key' => Hash::make($key),
            ];
        });
    }
}
