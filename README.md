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

```php
use Token\JWK\KeyFactory;
use Token\JWK\KeySet;

$publicKey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBg...
-----END PUBLIC KEY-----';

$key       = KeyFactory::createFromPem($publicKey, null, [
    'use' => 'sig',
]);

$keys = new KeySet();
$keys->addKey($key);
echo $keys->jsonSerialize();
```

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

```php
use Token\JWK\KeySetFactory;

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

```php
use Token\JWK\Key;
use Token\JWK\Thumbprint\ThumbprintURI;

Key::computeThumbprint(function (array $keys) {
    return md5(json_encode($keys));
});
Key::computeThumbprint([ThumbprintURI::class, 'computeThumbprintURI']);
```

## License

Nacosvel Contracts is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
