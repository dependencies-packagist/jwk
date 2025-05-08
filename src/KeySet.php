<?php

namespace Token\JWK;

use ArrayIterator;
use InvalidArgumentException;
use RuntimeException;
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
     *
     * @return static
     */
    public function addKey(KeyContract $key): static
    {
        if ($key->getKeyId() && $this->containsKey($key->getKeyId(), $key->getPublicKeyUse())) {
            throw new InvalidArgumentException(sprintf(
                'Key with id `%s` and use `%s` already exists in the set',
                $key->getKeyId(),
                $key->getPublicKeyUse()
            ));
        }

        $this->keys[] = $key;

        return $this;
    }

    /**
     * @param string $kid
     * @param string $use
     *
     * @return bool
     */
    public function containsKey(string $kid, string $use = KeyContract::PUBLIC_KEY_USE_SIGNATURE): bool
    {
        try {
            $this->getKeyById($kid, $use);
        } catch (RuntimeException) {
            return false;
        }
        return true;
    }

    /**
     * @param string $kid
     * @param string $use
     *
     * @return KeyContract
     */
    public function getKeyById(string $kid, string $use = KeyContract::PUBLIC_KEY_USE_SIGNATURE): KeyContract
    {
        foreach ($this->getKeys() as $key) {
            if ($key->getKeyId() === $kid && $key->getPublicKeyUse() === $use) {
                return $key;
            }
        }

        throw new RuntimeException("Key with id `$kid` and use `$use` not found");
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
