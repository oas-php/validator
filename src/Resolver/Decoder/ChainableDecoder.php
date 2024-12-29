<?php declare(strict_types=1);

namespace OAS\Resolver\Decoder;

use OAS\Resolver\Decoder;
use OAS\Resolver\DecodingError;
use stdClass;

class ChainableDecoder implements Decoder
{
    /** @var array<int, Decoder> */
    private array $decoders = [];

    /**
     * @param array<int, Decoder> $decoders
     */
    public function __construct(iterable $decoders = [])
    {
        foreach ($decoders as $decoder) {
            $this->addFormatDecoder($decoder);
        }
    }

    public function addFormatDecoder(Decoder $decoder): void
    {
        $this->decoders[] = $decoder;
    }

    public function decode(string $encoded): null|stdClass|array|bool|int|float
    {
        foreach ($this->decoders as $decoder) {
            try {
                return $decoder->decode($encoded);
            } catch (DecodingError $exception) {
                continue;
            }
        }

        throw new DecodingError($encoded, 'Non of registered decoder was able to handle provided resource');
    }
}
