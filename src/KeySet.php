<?php

namespace Token\JWK;

use ArrayIterator;
use InvalidArgumentException;
use Token\JWK\Contracts\Key;
use Token\JWK\Contracts\KeySet as KeySetContract;

class KeySet implements KeySetContract
{
    /**
     * @var Key[]
     */
    private array $keys = [];

    /**
     * @param Key $key
     *
     * @return static
     */
    public function addKey(Key $key): static
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
    public function containsKey(string $kid, string $use = Key::PUBLIC_KEY_USE_SIGNATURE): bool
    {
        return null !== $this->getKeyById($kid, $use);
    }

    /**
     * @param string $kid
     * @param string $use
     *
     * @return Key|null
     */
    public function getKeyById(string $kid, string $use = Key::PUBLIC_KEY_USE_SIGNATURE): ?Key
    {
        foreach ($this->getKeys() as $key) {
            if ($key->getKeyId() === $kid && $key->getPublicKeyUse() === $use) {
                return $key;
            }
        }

        return null;
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
     * @return Key[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getKeys());
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function jsonSerialize(): array
    {
        $ret = [];

        foreach ($this->getKeys() as $key) {
            $ret[$key->getKeyId()] = $key->jsonSerialize();
        }

        return [
            'keys' => array_values($ret),
        ];
    }

    public function toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_PRETTY_PRINT);
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
