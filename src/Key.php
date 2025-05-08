<?php

namespace Token\JWK;

use Closure;
use GmTLS\CryptoKit\KeypairParser;
use Token\JWK\Contracts\Key as KeyContract;
use Token\JWK\Thumbprint\ThumbprintURI;

class Key implements KeyContract
{
    protected static ?Closure $computeThumbprint = null;

    public function __construct(
        private array   $keys = [],
        private ?string $passphrase = null,
    )
    {
        if (!array_key_exists('kid', $this->keys)) {
            $this->keys['kid'] = self::keyIDFromThumbprint($keys);
        }
    }

    public static function computeThumbprint(callable $callback): void
    {
        self::$computeThumbprint = Closure::fromCallable($callback);
    }

    public static function keyIDFromThumbprint(array $keys): string
    {
        if (is_null(self::$computeThumbprint)) {
            return ThumbprintURI::computeThumbprintURI($keys);
        }
        return call_user_func(self::$computeThumbprint, $keys);
    }

    /**
     * {@inheritdoc}
     */
    public function setKeyType(string $kty = null): static
    {
        if (is_null($kty)) {
            unset($this->keys['kty']);
        } else {
            $this->keys['kty'] = $kty;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyType(): ?string
    {
        return $this->keys['kty'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeyId(string $kid = null): static
    {
        if (is_null($kid)) {
            unset($this->keys['kid']);
        } else {
            $this->keys['kid'] = $kid;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyId(): ?string
    {
        return $this->keys['kid'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublicKeyUse(string $use = null): static
    {
        if (is_null($use)) {
            unset($this->keys['use']);
        } else {
            $this->keys['use'] = $use;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKeyUse(): ?string
    {
        return $this->keys['use'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlgorithm(string $alg = null): static
    {
        if (is_null($alg)) {
            unset($this->keys['alg']);
        } else {
            $this->keys['alg'] = $alg;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlgorithm(): ?string
    {
        return $this->keys['alg'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurveName(string $crv): static
    {
        $this->keys['crv'] = $crv;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurveName(): ?string
    {
        return $this->keys['crv'] ?? null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->keys;
    }

    /**
     * Returns an array presentation of the key.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return KeypairParser::load($this->toString())->getPublicKey();
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return KeypairParser::load($this->toString())->getPrivateKey();
    }
}
