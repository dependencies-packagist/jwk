<?php

namespace Token\JWK\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

interface KeySet extends Countable, JsonSerializable, IteratorAggregate, Stringable
{
    public function addKey(Key $key): static;

    public function getKeys(): array;
}
