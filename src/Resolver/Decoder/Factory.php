<?php declare(strict_types=1);

namespace OAS\Resolver\Decoder;

use OAS\Resolver\Decoder;

final class Factory
{
    public static function create(): Decoder
    {
        $encoder = new Decoder\ChainableDecoder();

        if (function_exists('\json_encode')) {
            $encoder->addFormatDecoder(new Decoder\JsonDecoder());
        }

        if (class_exists('\Symfony\Component\Yaml\Yaml')) {
            $encoder->addFormatDecoder(new Decoder\YamlDecoder());
        }

        return $encoder;
    }
}
