<?php

namespace Token\JWK\Thumbprint;

use GmTLS\CryptoKit\Encoding\Encoder;
use InvalidArgumentException;

class Thumbprint
{
    public static function compute(array $jwk, string $alg = 'sha256'): string
    {
        $jwk = array_filter($jwk, fn($key) => $key != 'kid', ARRAY_FILTER_USE_KEY);

        ksort($jwk);

        $json = json_encode($jwk, JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new InvalidArgumentException('Failed to encode thumbprint JSON.');
        }

        $digest = hash($alg, $json, true);

        return (new Encoder())->base64UrlEncode($digest);
    }

    public static function getHashAlgorithm(string $algorithm = 'sha256'): string
    {
        return match ($algorithm) {
            'sha1' => 'sha-1',
            'sha256' => 'sha-256',
            'sha512' => 'sha-512',
            default => throw new InvalidArgumentException('Unsupported algorithm')
        };
    }
}
