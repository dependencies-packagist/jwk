<?php

namespace Token\JWK;

use ArrayIterator;
use Token\JWK\Contracts\Key as KeyContract;
use Token\JWK\Contracts\KeySet as KeySetContract;

class KeySet implements KeySetContract
{
    /**
     * @var KeyContract[]
     */
    private array $keys = [];

    /**
     * @param KeyContract $key
     * @param bool        $replace
     *
     * @return static
     */
    public function addKey(KeyContract $key, bool $replace = false): static
    {
        if ($replace && $this->containsKey($key->getKeyId(), $key->getPublicKeyUse())) {
            $this->removeKeyById($key->getKeyId(), $key->getPublicKeyUse());
        }

        $this->keys[] = $key;

        return $this;
    }

    /**
     * @param KeyContract $key
     *
     * @return static
     */
    public function removeKey(KeyContract $key): static
    {
        return $this->removeKeyById($key->getKeyId(), $key->getPublicKeyUse());
    }

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return static
     */
    public function removeKeyById(string $kid, string $use = null): static
    {
        return array_filter($this->keys, function (KeyContract $key) use ($kid, $use) {
            return false === $this->matchesKeyConditions($key, $kid, $use);
        });
    }

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return bool
     */
    public function containsKey(string $kid, string $use = null): bool
    {
        return $this->getKeyById($kid, $use) !== null;
    }

    /**
     * @param string      $kid
     * @param string|null $use
     *
     * @return KeyContract|null
     */
    public function getKeyById(string $kid, string $use = null): ?KeyContract
    {
        foreach ($this->getKeys() as $key) {
            if ($this->matchesKeyConditions($key, $kid, $use)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param KeyContract $key
     * @param string      $kid
     * @param string|null $use
     *
     * @return bool
     */
    public function matchesKeyConditions(KeyContract $key, string $kid, string $use = null): bool
    {
        return $key->getKeyId() === $kid && (is_null($use) || $key->getPublicKeyUse() === $use);
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return array_values($this->keys);
    }

    /**
     * @inheritdoc
     * @return int
     */
    public function count(): int
    {
        return count($this->getKeys());
    }

    /**
     * @inheritdoc
     * @return KeyContract[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getKeys());
    }

    public function toArray(): array
    {
        $ret = [];

        foreach ($this->getKeys() as $key) {
            $ret[$key->getKeyId()] = $key->toArray();
        }

        return [
            'keys' => array_values($ret),
        ];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
