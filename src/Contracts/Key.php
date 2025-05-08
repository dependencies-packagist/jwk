<?php

namespace Token\JWK\Contracts;

use JsonSerializable;
use Stringable;

interface Key extends JsonSerializable, Stringable
{
    public const KEY_TYPE_RSA = 'RSA';

    public const KEY_TYPE_EC = 'EC';

    public const PUBLIC_KEY_USE_SIGNATURE  = 'sig';
    public const PUBLIC_KEY_USE_ENCRYPTION = 'enc';

    public static function computeThumbprint(callable $callback): void;

    public static function keyIDFromThumbprint(array $keys): string;

    /**
     * Sets the key type, ie. the value for the `kty` field.
     *
     * See the KEY_TYPE_* constants for reference.
     *
     * @param string|null $kty
     *
     * @return static
     */
    public function setKeyType(?string $kty): static;

    /**
     * Gets the key type, ie. the value of the `kty` field.
     *
     * @return string|null
     */
    public function getKeyType(): ?string;

    /**
     * Sets the key id, ie. the value of the `kid` field.
     *
     * @param string|null $kid
     *
     * @return static
     */
    public function setKeyId(?string $kid): static;

    /**
     * Gets the key id, ie. the value of the `kid` field.
     *
     * @return string|null
     */
    public function getKeyId(): ?string;

    /**
     * Sets the public key use, ie. the value of the `use` field.
     *
     * @param string|null $use
     *
     * @return static
     */
    public function setPublicKeyUse(?string $use): static;

    /**
     * Gets the public key use, ie. the value of the `use` field.
     *
     * @return string|null
     */
    public function getPublicKeyUse(): ?string;

    /**
     * Sets the cryptographic algorithm used to sign the key, ie. the value of the `alg` field.
     *
     * @param string|null $alg
     *
     * @return static
     */
    public function setAlgorithm(?string $alg): static;

    /**
     * Gets the cryptographic algorithm used to sign the key, ie. the value of the `alg` field.
     *
     * @return string|null
     */
    public function getAlgorithm(): ?string;

    /**
     * @param string $crv
     *
     * @return static
     */
    public function setCurveName(string $crv): static;

    /**
     * @return string|null
     */
    public function getCurveName(): ?string;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string;

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string;
}
