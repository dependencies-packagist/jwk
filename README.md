# JSON Web Key

A simple library to work with JSON Web Key based on the [RFC 7517](https://tools.ietf.org/html/rfc7517).

[![GitHub Tag](https://img.shields.io/github/v/tag/dependencies-packagist/jwk)](https://github.com/dependencies-packagist/jwk/tags)
[![Total Downloads](https://img.shields.io/packagist/dt/token/jwk?style=flat-square)](https://packagist.org/packages/token/jwk)
[![Packagist Version](https://img.shields.io/packagist/v/token/jwk)](https://packagist.org/packages/token/jwk)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/token/jwk)](https://github.com/dependencies-packagist/jwk)
[![Packagist License](https://img.shields.io/github/license/dependencies-packagist/jwk)](https://github.com/dependencies-packagist/jwk)

## Installation

You can install the package via [Composer](https://getcomposer.org/):

```bash
composer require token/jwk
```

## Usage

### Create a key

```php
use Token\JWK\Key;

$key = new Key();
$key->setKeyId('key-1');
$key->setKeyType('RSA');
$key->setPublicKeyUse('sig');
$key->push([
    "n" => "z24W4Hs...",
    "e" => "AQAB",
]);
$key->put('other', 'other-value');

echo $key->jsonSerialize();
```

Output:

```json
{
    "kid": "key-1",
    "use": "sig",
    "kty": "RSA",
    "n": "z24W4Hs...",
    "e": "AQAB",
    "other": "other-value"
}
```

### Create a KeySet from a publicKey

```php
use Token\JWK\KeyFactory;
use Token\JWK\KeySet;

$publicKey = file_get_contents('public.pem');
$key       = KeyFactory::createFromPem($publicKey);
$keys      = new KeySet();
$keys->addKey($key);
echo $keys->jsonSerialize();
```

Output:

```json
{
    "keys": [
        {
            "use": "sig",
            "kty": "RSA",
            "n": "...",
            "e": "...",
            "kid": "urn:ietf:params:oauth:jwk-thumbprint:sha-256:ef-cEOUom1NztLRBBWGQjmRyaYCK4NwggwOdw-CXfAc"
        }
    ]
}
```

### Parse a KeySet

```php
use Token\JWK\KeySetFactory;

$keys = $keys->jsonSerialize();
foreach (KeySetFactory::createFromJSON($keys) as $key) {
    var_dump($key->getKeyId());
    var_dump($key->getPrivateKey());
    var_dump($key->getPublicKey());
}
var_dump(KeySetFactory::createFromJSON($keys)->toArray());
var_dump(KeySetFactory::createFromJSON($keys)->jsonSerialize());
var_dump(KeySetFactory::createFromJSON($keys)->toString());
var_dump(KeySetFactory::createFromJSON($keys)->getKeyById('S7_qdQ')->getKeyType());
var_dump(KeySetFactory::createFromJSON($keys)->getKeyById('S7_qdQ')->getPublicKey());
```

### Customizing Key Thumbprint Calculation

The `Key::computeThumbprint()` static method allows you to customize how a JWK (JSON Web Key) thumbprint is computed. This is useful when you want to define your own logic for generating a unique key identifier.

#### Example 1: Use a Custom Closure

You can define your own thumbprint logic using a closure:

```php
use Token\JWK\Key;

Key::computeThumbprint(function (array $keyData) {
    return md5(json_encode($keyData));
});
```

This will override the default thumbprint behavior, using an MD5 hash of the serialized key data.

#### Example 2: Use a Static Method as Callable

You can also use a static method from a custom class:

```php
use Token\JWK\Key;
use Token\JWK\Thumbprint\ThumbprintURI;

Key::computeThumbprint([ThumbprintURI::class, 'computeThumbprintURI']);
```

This approach delegates the thumbprint generation to the `computeThumbprintURI()` method inside the `ThumbprintURI` class.

#### Note

* The callback must be a `callable` that receives an array representing the JWK and returns a string thumbprint.

---

## License

Nacosvel Contracts is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
