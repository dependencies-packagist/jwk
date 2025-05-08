<?php

namespace Token\JWK\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

interface KeySet extends Countable, JsonSerializable, IteratorAggregate, Stringable
{
    public function addKey(Key $key): static;

    public function containsKey(string $kid, string $use = Key::PUBLIC_KEY_USE_SIGNATURE): bool;

    public function getKeyById(string $kid, string $use = Key::PUBLIC_KEY_USE_SIGNATURE): ?Key;

    public function getKeys(): array;

    public function toArray(): array;

    public function toString(): string;
}
