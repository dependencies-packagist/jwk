<?php

namespace Token\JWK;

use Token\JWK\Contracts\KeyFactory as KeyFactoryContract;
use Token\JWK\Contracts\KeySet as KeySetContract;
use Token\JWK\Contracts\KeySetFactory as KeySetFactoryContract;

class KeySetFactory implements KeySetFactoryContract
{
    protected static ?KeyFactoryContract $keyFactory = null;

    public function __construct(
        KeyFactoryContract $keyFactory = null,
    )
    {
        self::$keyFactory = $keyFactory ?? new KeyFactory();
    }

    /**
     * @param KeyFactoryContract $keyFactory
     *
     * @return static
     */
    public static function setKeyFactory(KeyFactoryContract $keyFactory): static
    {
        return new static($keyFactory);
    }

    /**
     * @return KeyFactoryContract
     */
    protected static function getKeyFactory(): KeyFactoryContract
    {
        return self::$keyFactory ??= new KeyFactory();
    }

    /**
     * @param string      $json
     * @param string|null $passphraseKey
     *
     * @return KeySetContract
     */
    public static function createFromJSON(string $json, string $passphraseKey = null): KeySetContract
    {
        $assoc = json_decode($json, true);

        $instance = new KeySet();

        if (!is_array($assoc) || !array_key_exists('keys', $assoc)) {
            return $instance;
        }

        foreach ($assoc['keys'] as $key) {
            $passphrase  = $key[$passphraseKey] ?? null;
            $keyFromJson = self::getKeyFactory()->createFromJson(json_encode($key), $passphrase);
            $instance->addKey($keyFromJson);
        }

        return $instance;
    }
}
