<?php

namespace Token\JWK;

use Token\JWK\Contracts\KeyFactory as KeyFactoryContract;
use Token\JWK\Contracts\KeySet as KeySetContract;
use Token\JWK\Contracts\KeySetFactory as KeySetFactoryContract;

class KeySetFactory implements KeySetFactoryContract
{
    public function __construct(
        private ?KeyFactoryContract $keyFactory = null,
    )
    {
        $this->keyFactory = $keyFactory ?? new KeyFactory();
    }

    /**
     * @param KeyFactoryContract $keyFactory
     *
     * @return static
     */
    public function setKeyFactory(KeyFactoryContract $keyFactory): static
    {
        $this->keyFactory = $keyFactory;

        return $this;
    }

    /**
     * @param string      $json
     * @param string|null $passphraseKey
     *
     * @return KeySetContract
     */
    public function createFromJSON(string $json, string $passphraseKey = null): KeySetContract
    {
        $assoc = json_decode($json, true);

        $instance = new KeySet();

        if (!is_array($assoc) || !array_key_exists('keys', $assoc)) {
            return $instance;
        }

        foreach ($assoc['keys'] as $key) {
            $passphrase  = $key[$passphraseKey] ?? null;
            $keyFromJson = $this->keyFactory->createFromJson(json_encode($key), $passphrase);
            $instance->addKey($keyFromJson);
        }

        return $instance;
    }
}
