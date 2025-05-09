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

### 创建一个 Key / Create a Key

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

输出 / Output:

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

### 从 PEM 公钥创建 KeySet / Create a KeySet from Public Key

```php
use Token\JWK\KeyFactory;
use Token\JWK\KeySet;

$publicKey = file_get_contents('public.pem');
$key       = KeyFactory::createFromPem($publicKey);
$keys      = new KeySet();
$keys->addKey($key);
echo $keys->jsonSerialize();
```

输出 / Output:

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

### 解析 KeySet / Parse a KeySet

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

### 自定义 Thumbprint 算法 / Customizing Thumbprint Generation

您可以使用 `Key::computeThumbprint()` 方法覆盖默认的 thumbprint 生成逻辑。

#### 方式 1: 使用闭包 / With a Closure

```php
use Token\JWK\Key;

Key::computeThumbprint(function (array $keyData) {
    return md5(json_encode($keyData));
});
```

#### 方式 2: 使用类静态方法 / With Static Method

```php
use Token\JWK\Key;
use Token\JWK\Thumbprint\ThumbprintURI;

Key::computeThumbprint([ThumbprintURI::class, 'computeThumbprintURI']);
```

#### 注意 / Note:

* 回调必须是 `callable`，接受一个数组并返回字符串。

---

## License

Nacosvel Contracts is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
