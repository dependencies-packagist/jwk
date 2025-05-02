<?php

namespace Token\JWK\Contracts;

interface KeySetFactory
{
    public function setKeyFactory(KeyFactory $keyFactory): static;

    public function createFromJSON(string $json, string $passphraseKey = null): KeySet;
}
