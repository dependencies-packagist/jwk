<?php

namespace Token\JWK\Contracts;

use Closure;

interface KeyFactory
{
    public static function extend(string $provider, Closure $callback): void;

    public static function createFromPem(string $pem, string $passphrase = null, array $options = []): Key;

    public static function createFromJson(string $json, string $passphrase = null): Key;
}
