<?php declare(strict_types=1);

namespace OAS\Resolver\Decoder;

use JsonException;
use OAS\Resolver\Decoder;
use OAS\Resolver\DecodingError;
use stdClass;

class JsonDecoder implements Decoder
{
    public function __construct(public readonly int $depth = 512, public readonly int $options = 0)
    {
    }

    /**
     * @inheritdoc
     */
    public function decode(string $encoded): null|stdClass|array|bool|int|float
    {
        try {
            return json_decode($encoded, true, $this->depth, $this->options | JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new DecodingError($encoded, $jsonException->getMessage(), 0, $jsonException);
        }
    }
}
