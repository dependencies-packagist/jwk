<?php

namespace Token\JWK\Thumbprint;

use InvalidArgumentException;
use Standards\NSS;
use Standards\URN;

class ThumbprintURI
{
    public static function computeThumbprintURI(array $jwk, string $alg = 'sha256'): string
    {
        $thumbprint = Thumbprint::compute($jwk, $alg);
        $alg        = Thumbprint::getHashAlgorithm($alg);
        return URN::build('ietf', NSS::build(type: $alg, value: $thumbprint));
    }

    public static function parse(string $uri): NSS
    {
        $pattern = '/^urn:ietf:params:oauth:jwk-thumbprint:([^:]+):(.+)$/';

        if (!preg_match($pattern, $uri, $matches)) {
            throw new InvalidArgumentException("Invalid JWK thumbprint URN: {$uri}");
        }

        return NSS::parse(URN::parse($uri)->getNSS());
    }
}
