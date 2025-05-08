<?php

namespace Token\JWK\Thumbprint;

use InvalidArgumentException;
use Standards\URN\NamespaceSpecificString;
use Standards\URN\UniformResourceName;

class ThumbprintURI
{
    public static function computeThumbprintURI(array $jwk, string $alg = 'sha256'): string
    {
        $thumbprint = Thumbprint::compute($jwk, $alg);
        $alg        = Thumbprint::getHashAlgorithm($alg);
        return UniformResourceName::build('ietf', NamespaceSpecificString::build(type: $alg, value: $thumbprint));
    }

    public static function parse(string $uri): UniformResourceName
    {
        $pattern = '/^urn:ietf:params:oauth:jwk-thumbprint:([^:]+):(.+)$/';

        if (!preg_match($pattern, $uri, $matches)) {
            throw new InvalidArgumentException("Invalid JWK thumbprint URN: {$uri}");
        }

        return UniformResourceName::parse($uri);
    }
}
