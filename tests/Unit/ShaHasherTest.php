<?php

use PaulisRatnieks\ApiKeyAuth\ShaHasher;

beforeEach(function (): void {
    $this->hasher = new ShaHasher();
});

test('SHAHasher hashes and checks hashes', function (): void {
    $value = fake()->uuid();
    $hashedValue = $this->hasher->make($value);
    $this->assertNotEquals($value, $hashedValue);
    $this->assertTrue($this->hasher->check($value, $hashedValue));
});
