<?php

use PaulisRatnieks\ApiKeyAuth\ApiClient;
use PaulisRatnieks\ApiKeyAuth\Casts\AsList;

test('AsList with empty string get returns empty collection', function (): void {
    $value = (new AsList())->get(new ApiClient(), '', '', []);
    $this->assertTrue($value->isEmpty());
});

test('AsList with non string value returns empty string', function (): void {
    $value = (new AsList())->set(new ApiClient(), '', null, []);
    $this->assertEmpty($value);
});
