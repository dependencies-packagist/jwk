<?php

namespace Token\JWK;

use Closure;
use GmTLS\CryptoKit\KeypairLoader;
use GmTLS\CryptoKit\KeypairParser;
use InvalidArgumentException;
use Token\JWK\Contracts\Key as KeyContract;
use Token\JWK\Contracts\KeyFactory as KeyFactoryContract;

class KeyFactory implements KeyFactoryContract
{
    /**
     * The registered custom provider creators.
     */
    protected static array $customProviderCreators = [];

    /**
     * Register a custom provider creator Closure.
     *
     * @param string  $provider
     * @param Closure $callback
     *
     * @return void
     */
    public static function extend(string $provider, Closure $callback): void
    {
        self::$customProviderCreators[$provider] = $callback;
    }

    public static function createFromPem(string $pem, string $passphrase = null, array $options = []): KeyContract
    {
        $isPublicKey = true;
        if (!$resource = openssl_pkey_get_public($pem)) {
            $resource    = openssl_pkey_get_private($pem, $passphrase);
            $isPublicKey = false;
        }
        if ($resource === false) {
            throw new InvalidArgumentException('Invalid PEM key.');
        }

        $details = openssl_pkey_get_details($resource);
        if ($details === false) {
            throw new InvalidArgumentException('Failed to get key details.');
        }

        if (isset(self::$customProviderCreators[$details['type']])) {
            $keys = call_user_func(self::$customProviderCreators[$details['type']], $pem, $passphrase, $details, $isPublicKey);
        } else {
            $keys = match ($details['type']) {
                OPENSSL_KEYTYPE_RSA, OPENSSL_KEYTYPE_EC => self::getJSONWebKey($pem, $passphrase, $details, $isPublicKey),
                default => throw new InvalidArgumentException("Unsupported key type: {$details['type']}"),
            };
        }

        return new Key(array_merge($options, $keys), $passphrase);
    }

    protected static function getJSONWebKey(string $pem, string $passphrase = null, array $details = [], bool $isPublicKey = true): array
    {
        if ($isPublicKey) {
            $keys = KeypairParser::create(KeypairLoader::fromPublicKeyString($pem))->toPublicKey('JWK');
        } else {
            $keys = KeypairParser::create(KeypairLoader::fromPrivateKeyString($pem, $passphrase))->toPrivateKey('JWK');
        }

        $keys = json_decode($keys, true);

        foreach ($keys['keys'] ?? [] as $key) {
            return $key;
        }

        throw new InvalidArgumentException('Failed to get JSON Web Key.');
    }

    public static function createFromJson(string $json, string $passphrase = null): KeyContract
    {
        return new Key(self::getKeyFromJson($json), $passphrase);
    }

    protected static function getKeyFromJson(string $json): array
    {
        return json_decode($json, true);
    }
}
