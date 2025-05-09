<?php

namespace Token\JWK\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

interface KeySet extends Countable, JsonSerializable, IteratorAggregate, Stringable
{
    public function addKey(Key $key, bool $replace = false): static;

    /**
     * @param Key $key
     *
     * @return static
     */
    public function removeKey(Key $key): static;

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return static
     */
    public function removeKeyById(string $kid, string $use = null): static;

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return bool
     */
    public function containsKey(string $kid, string $use = null): bool;

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return Key|null
     */
    public function getKeyById(string $kid, string $use = null): ?Key;

    /**
     * @param Key         $key
     * @param string      $kid
     * @param string|null $use
     *
     * @return bool
     */
    public function matchesKeyConditions(Key $key, string $kid, string $use = null): bool;

    public function getKeys(): array;

    public function toArray(): array;

    public function toString(): string;
}
