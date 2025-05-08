<?php

namespace Token\JWK\Contracts;

interface KeySetFactory
{
    public static function setKeyFactory(KeyFactory $keyFactory): static;

    public static function createFromJSON(string $json, string $passphraseKey = null): KeySet;
}
